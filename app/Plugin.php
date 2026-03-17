<?php

declare(strict_types=1);

namespace UMANI;

use UMANI\I18n\I18nService;
use UMANI\Option\OptionRegistrar;
use UMANI\Updater\Updater;

class Plugin
{
    private string $slug;

    public function __construct()
    {
        $this->slug = UMANI_CC_SLUG;

        add_action('init', [$this, 'loadTextdomain']);
        add_action('admin_init', [$this, 'registerOptions']);
        add_action('admin_menu', [$this, 'registerAdminMenus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('upgrader_process_complete', [$this, 'purgeLitespeedCache'], 10, 2);

        if (!is_admin()) {
            new Front($this->slug);
        }

        $this->initUpdater();
    }

    public function loadTextdomain(): void
    {
        load_plugin_textdomain('umani-cookie-control', false, basename(UMANI_CC_DIR) . '/languages');
    }

    public function registerOptions(): void
    {
        $config = require UMANI_CC_DIR . '/app/Config/options.php';
        $i18n = new I18nService();
        $registrar = new OptionRegistrar($this->slug, $i18n);
        $registrar->register($config);
    }

    public function registerAdminMenus(): void
    {
        $admin = new Admin($this->slug);
        $admin->addMenus();
    }

    public function enqueueAdminAssets(string $hook): void
    {
        if (!str_contains($hook, $this->slug)) {
            return;
        }

        $cmSettings['codeEditor'] = wp_enqueue_code_editor(['type' => 'text/html']);
        wp_localize_script('jquery', 'cm_settings', $cmSettings);
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        wp_enqueue_script(
            'umani-cc-admin',
            UMANI_CC_URL . 'js/admin.js',
            ['jquery', 'wp-color-picker'],
            UMANI_CC_VERSION,
            true
        );

        wp_localize_script('umani-cc-admin', 'umaniCCAdmin', [
            'bannerActiveId' => $this->slug . '-banner-active',
        ]);
    }

    public function purgeLitespeedCache(object $upgrader, array $options): void
    {
        if (($options['action'] ?? '') !== 'update' || ($options['type'] ?? '') !== 'plugin') {
            return;
        }

        foreach ($options['plugins'] ?? [] as $plugin) {
            if ($plugin === plugin_basename(UMANI_CC_FILE)) {
                do_action('litespeed_purge_all');
                break;
            }
        }
    }

    private function initUpdater(): void
    {
        $username = get_option($this->slug . '-username');
        $token = get_option($this->slug . '-token');

        if (!$username || !$token) {
            return;
        }

        add_action('admin_init', function () use ($username, $token) {
            new Updater(UMANI_CC_FILE, UMANI_CC_DIR, $username, $token);
        }, 20);
    }
}
