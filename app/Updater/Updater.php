<?php

declare(strict_types=1);

namespace UMANI\Updater;

class Updater
{
    private string $pluginSlug;
    private string $pluginDirPath;
    private string $pluginFile;
    private array $pluginData = [];
    private string $gitHubUsername;
    private string $repositoryName;
    private ?array $githubAPIResult = null;
    private string $accessToken;
    private ?string $zipballUrl = null;
    private bool $pluginActivated = false;

    private const TRANSIENT_RELEASE = 'umani_cc_github_release';
    private const TRANSIENT_AUTH = 'umani_cc_github_auth';
    private const TRANSIENT_TTL = 43200; // 12 hours

    public function __construct(string $file, string $dir, string $gitHubUsername, string $accessToken)
    {
        $this->pluginSlug = plugin_basename($dir);
        $this->pluginDirPath = plugin_dir_path($file);
        $this->pluginFile = plugin_basename($file);
        $this->gitHubUsername = $gitHubUsername;
        $this->repositoryName = 'umani-cookie-control';
        $this->accessToken = $accessToken;

        $this->initPluginData($file);

        if (!$this->isAuthenticated()) {
            return;
        }

        add_filter('pre_set_site_transient_update_plugins', [$this, 'setTransient'], 10, 1);
        add_filter('plugins_api', [$this, 'setPluginInfo'], 10, 3);
        add_filter('upgrader_post_install', [$this, 'postInstall'], 10, 3);
        add_filter('upgrader_pre_install', [$this, 'preInstall'], 10);
        add_filter('plugin_auto_update_setting_html', [$this, 'showAutoUpdateOption'], 10, 2);
    }

    private function initPluginData(string $file): void
    {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $this->pluginData = \get_plugin_data($file);
    }

    private function isAuthenticated(): bool
    {
        $cached = get_transient(self::TRANSIENT_AUTH);
        if ($cached !== false) {
            return (bool) $cached;
        }

        $response = wp_remote_get('https://api.github.com/user', [
            'headers' => ['Authorization' => 'token ' . $this->accessToken],
        ]);

        $isAuth = !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
        set_transient(self::TRANSIENT_AUTH, $isAuth ? 1 : 0, self::TRANSIENT_TTL);

        return $isAuth;
    }

    private function getRepoReleaseInfo(): void
    {
        if ($this->githubAPIResult !== null) {
            return;
        }

        $cached = get_transient(self::TRANSIENT_RELEASE);
        if ($cached !== false && is_array($cached)) {
            $this->githubAPIResult = $cached;
            $this->zipballUrl = $cached['zipball_url'] ?? null;
            return;
        }

        $url = sprintf(
            'https://api.github.com/repos/%s/%s/releases/latest',
            $this->gitHubUsername,
            $this->repositoryName
        );

        $response = wp_remote_get($url, [
            'headers' => ['Authorization' => 'token ' . $this->accessToken],
        ]);

        if (is_wp_error($response)) {
            return;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($body) || empty($body['tag_name'])) {
            return;
        }

        $this->githubAPIResult = $body;
        $this->zipballUrl = $body['zipball_url'] ?? null;

        set_transient(self::TRANSIENT_RELEASE, $body, self::TRANSIENT_TTL);
    }

    public function setTransient($transient)
    {
        if (!property_exists($transient, 'checked') || empty($transient->checked)) {
            return $transient;
        }

        if (!isset($transient->checked[$this->pluginFile]) || empty($this->pluginData)) {
            return $transient;
        }

        $this->getRepoReleaseInfo();

        if (empty($this->githubAPIResult['tag_name'])) {
            return $transient;
        }

        $doUpdate = version_compare(
            $this->githubAPIResult['tag_name'],
            $transient->checked[$this->pluginFile],
            'gt'
        );

        if (!$doUpdate) {
            return $transient;
        }

        if (is_array($this->pluginData) && !empty($this->pluginData['PluginURI'])) {
            $transient->response[$this->pluginFile] = (object) [
                'id'          => $this->pluginFile,
                'slug'        => $this->pluginSlug,
                'plugin'      => $this->pluginFile,
                'new_version' => $this->githubAPIResult['tag_name'],
                'url'         => $this->pluginData['PluginURI'],
                'package'     => $this->zipballUrl,
            ];
        }

        return $transient;
    }

    public function setPluginInfo($false, $action, $response)
    {
        if ($action !== 'plugin_information') {
            return $false;
        }

        if (empty($response->slug) || $response->slug !== $this->pluginSlug) {
            return $false;
        }

        $this->getRepoReleaseInfo();

        if (empty($this->githubAPIResult) || empty($this->pluginData)) {
            return $false;
        }

        $response->last_updated = $this->githubAPIResult['published_at'] ?? '';
        $response->slug = $this->pluginSlug;
        $response->name = $this->pluginData['Name'] ?? '';
        $response->version = $this->githubAPIResult['tag_name'] ?? '';
        $response->author = $this->pluginData['AuthorName'] ?? '';
        $response->author_profile = $this->pluginData['AuthorURI'] ?? '';
        $response->homepage = $this->pluginData['PluginURI'] ?? '';
        $response->short_description = $this->pluginData['Description'] ?? '';
        $response->sections = [
            'Description' => $this->pluginData['Description'] ?? '',
            'Updates'     => $this->githubAPIResult['body'] ?? '',
        ];
        $response->download_link = $this->zipballUrl;
        $response->trunk = $this->zipballUrl;

        return $response;
    }

    public function postInstall($true, $hookExtra, $result)
    {
        global $wp_filesystem;

        $wp_filesystem->move($result['destination'], $this->pluginDirPath);
        $result['destination'] = $this->pluginDirPath;
        $result['destination_name'] = basename($this->pluginDirPath);

        if ($this->pluginActivated) {
            activate_plugin($this->pluginFile);
        }

        // Clear cached release info
        delete_transient(self::TRANSIENT_RELEASE);

        return $result;
    }

    public function preInstall($true): bool
    {
        $this->pluginActivated = is_plugin_active($this->pluginFile);
        return $true;
    }

    public function showAutoUpdateOption(string $html, string $pluginFile): string
    {
        if ($this->pluginFile !== $pluginFile) {
            return $html;
        }

        $autoUpdates = (array) get_site_option('auto_update_plugins', []);
        $isEnabled = in_array($pluginFile, $autoUpdates, true);

        return sprintf(
            '<a href="%s" data-wp-action="%s" class="toggle-auto-update aria-button-if-js">'
            . '<span class="dashicons dashicons-update spin hidden" aria-hidden="true"></span>'
            . '<span class="label">%s</span></a>',
            wp_nonce_url('plugins.php?action=toggle-auto-update&amp;plugin=' . urlencode($pluginFile), 'updates'),
            $isEnabled ? 'disable' : 'enable',
            $isEnabled ? __('Disable auto-updates') : __('Enable auto-updates')
        );
    }
}
