<?php

declare(strict_types=1);

namespace UMANI;

use UMANI\I18n\I18nService;

class Front
{
    private string $slug;
    private I18nService $i18n;
    private bool $bodyOpened = false;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->i18n = new I18nService();

        add_action('wp_head', [$this, 'renderHeadCode'], 1);
        add_action('wp_body_open', [$this, 'markBodyOpened']);
        add_action('wp_body_open', [$this, 'renderBodyCode']);
        add_action('wp_footer', [$this, 'renderBodyCodeFallback']);

        if ($this->isBannerEnabled()) {
            add_action('wp_body_open', [$this, 'renderBanner']);
            add_action('wp_footer', [$this, 'renderBannerFallback']);
            add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        }
    }

    public function markBodyOpened(): void
    {
        $this->bodyOpened = true;
    }

    public function renderHeadCode(): void
    {
        if ($this->getOption('banner-active')) {
            $this->renderConsentModeDefault();
            $this->renderConsentModeUpdate();
        }

        $headCode = $this->getOption('head');
        if ($headCode) {
            echo $headCode;
        }
    }

    public function renderBodyCode(): void
    {
        $bodyCode = $this->getOption('body');
        if ($bodyCode) {
            echo $bodyCode;
        }
    }

    public function renderBodyCodeFallback(): void
    {
        if (!$this->bodyOpened) {
            $this->renderBodyCode();
        }
    }

    public function renderBanner(): void
    {
        $vars = $this->getBannerVars();
        extract($vars, EXTR_SKIP);
        include UMANI_CC_DIR . '/views/banner.php';
    }

    public function renderBannerFallback(): void
    {
        if (!$this->bodyOpened) {
            $this->renderBanner();
        }
    }

    public function enqueueAssets(): void
    {
        wp_enqueue_style(
            'umani-cc-banner',
            UMANI_CC_URL . 'css/banner.css',
            [],
            UMANI_CC_VERSION
        );

        $vars = $this->getBannerVars();
        $inlineCSS = ":root {
            --umani-cc-bg: {$vars['bannerColor']};
            --umani-cc-text: {$vars['bannerTextColor']};
            --umani-cc-link: {$vars['bannerLinkColor']};
            --umani-cc-btn-bg: {$vars['bannerButtonColor']};
            --umani-cc-btn-text: {$vars['bannerButtonTextColor']};
        }";
        wp_add_inline_style('umani-cc-banner', $inlineCSS);

        $categories = require UMANI_CC_DIR . '/app/Config/consent-categories.php';
        $lang = $this->i18n->getCurrentLanguage();

        $categoriesForJs = [];
        foreach ($categories as $key => $cat) {
            $categoriesForJs[$key] = [
                'required'  => $cat['required'],
                'gtmTypes'  => $cat['gtm_types'],
            ];
        }

        wp_enqueue_script(
            'umani-cc-consent',
            UMANI_CC_URL . 'js/consent.js',
            [],
            UMANI_CC_VERSION,
            true
        );

        wp_localize_script('umani-cc-consent', 'umaniCC', [
            'categories' => $categoriesForJs,
        ]);
    }

    private function renderConsentModeDefault(): void
    {
        $categories = require UMANI_CC_DIR . '/app/Config/consent-categories.php';
        $consentDefaults = [];

        foreach ($categories as $cat) {
            $status = $cat['required'] ? 'granted' : 'denied';
            foreach ($cat['gtm_types'] as $type) {
                $consentDefaults[$type] = $status;
            }
        }

        $json = wp_json_encode($consentDefaults);
        ?>
        <!-- Umani Cookie Control - Consent Mode v2 Default -->
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){window.dataLayer.push(arguments);}

        (function(){
            var saved = null;
            try {
                var raw = document.cookie.match(/(?:^|; )umani_consent=([^;]*)/);
                if (raw) saved = JSON.parse(decodeURIComponent(raw[1]));
            } catch(e) {}

            var defaults = <?php echo $json; ?>;

            if (saved && typeof saved === 'object') {
                var categories = <?php echo wp_json_encode(
                    array_map(fn($c) => ['required' => $c['required'], 'gtmTypes' => $c['gtm_types']], $categories)
                ); ?>;

                for (var key in categories) {
                    if (categories[key].required) continue;
                    var granted = saved[key] === true;
                    categories[key].gtmTypes.forEach(function(type) {
                        defaults[type] = granted ? 'granted' : 'denied';
                    });
                }
            }

            gtag('consent', 'default', defaults);
        })();
        </script>
        <?php
    }

    private function renderConsentModeUpdate(): void
    {
        $categories = require UMANI_CC_DIR . '/app/Config/consent-categories.php';
        $categoriesJson = wp_json_encode(
            array_map(fn($c) => ['required' => $c['required'], 'gtmTypes' => $c['gtm_types']], $categories)
        );
        ?>
        <!-- Umani Cookie Control - Consent Update Function -->
        <script>
        function umaniUpdateConsent(choices) {
            var categories = <?php echo $categoriesJson; ?>;
            var update = {};

            for (var key in categories) {
                if (categories[key].required) continue;
                var granted = choices[key] === true;
                categories[key].gtmTypes.forEach(function(type) {
                    update[type] = granted ? 'granted' : 'denied';
                });
            }

            gtag('consent', 'update', update);
        }
        </script>
        <?php
    }

    private function getBannerVars(): array
    {
        $lang = $this->i18n->hasWpml() ? $this->i18n->getCurrentLanguage() : null;
        $categories = require UMANI_CC_DIR . '/app/Config/consent-categories.php';
        $currentLang = $this->i18n->getCurrentLanguage();

        $categoriesLabels = [];
        foreach ($categories as $key => $cat) {
            $categoriesLabels[$key] = [
                'label'       => $this->i18n->getDefault($cat['label'], $currentLang),
                'description' => $this->i18n->getDefault($cat['description'], $currentLang),
                'required'    => $cat['required'],
            ];
        }

        return [
            'bannerText'            => $this->getI18nOption('banner-text', $lang) ?: $this->getDefaultBannerText(),
            'textAccept'            => $this->getI18nOption('banner-accept-text', $lang) ?: $this->i18n->getDefault(['fr' => 'Accepter tout', 'en' => 'Accept all', 'es' => 'Aceptar todo'], $currentLang),
            'textReject'            => $this->getI18nOption('banner-reject-text', $lang) ?: $this->i18n->getDefault(['fr' => 'Refuser', 'en' => 'Reject', 'es' => 'Rechazar'], $currentLang),
            'textReadMore'          => $this->getI18nOption('banner-readMore-text', $lang) ?: $this->i18n->getDefault(['fr' => 'Politique de confidentialité', 'en' => 'Privacy policy', 'es' => 'Política de privacidad'], $currentLang),
            'textSettings'          => $this->getI18nOption('banner-settings-text', $lang) ?: $this->i18n->getDefault(['fr' => 'Paramétrer', 'en' => 'Settings', 'es' => 'Configurar'], $currentLang),
            'textSave'              => $this->getI18nOption('banner-save-text', $lang) ?: $this->i18n->getDefault(['fr' => 'Sauvegarder', 'en' => 'Save', 'es' => 'Guardar'], $currentLang),
            'privacyPageUrl'        => $this->getPrivacyPageUrl(),
            'bannerColor'           => sanitize_hex_color($this->getOption('banner-color') ?: '') ?: '#FFFFFF',
            'bannerTextColor'       => sanitize_hex_color($this->getOption('banner-text-color') ?: '') ?: '#333333',
            'bannerLinkColor'       => sanitize_hex_color($this->getOption('banner-link-color') ?: '') ?: '#0066CC',
            'bannerButtonColor'     => sanitize_hex_color($this->getOption('banner-button-color') ?: '') ?: '#222222',
            'bannerButtonTextColor' => sanitize_hex_color($this->getOption('banner-button-text-color') ?: '') ?: '#FFFFFF',
            'categories'            => $categoriesLabels,
        ];
    }

    private function isBannerEnabled(): bool
    {
        $privacyPage = get_option('wp_page_for_privacy_policy');

        return (bool) $this->getOption('banner-active')
            && $privacyPage
            && get_post_status($privacyPage) === 'publish';
    }

    private function getOption(string $name): mixed
    {
        return get_option($this->slug . '-' . $name, false);
    }

    private function getI18nOption(string $name, ?string $lang): mixed
    {
        $key = $this->slug . '-' . $name;
        if ($lang) {
            $key .= '-' . $lang;
        }

        return get_option($key, false);
    }

    private function getDefaultBannerText(): string
    {
        $siteName = get_bloginfo('name');
        $lang = $this->i18n->getCurrentLanguage();

        $defaults = [
            'fr' => $siteName . ' utilise des cookies pour vous offrir une expérience plus fluide, adapter les contenus à vos préférences et analyser les performances générales.',
            'en' => $siteName . ' uses cookies to offer you a smoother experience, tailor content to your preferences, and analyze overall performance.',
            'es' => $siteName . ' utiliza cookies para ofrecerte una experiencia más fluida, adaptar los contenidos a tus preferencias y analizar el rendimiento general.',
        ];

        return $this->i18n->getDefault($defaults, $lang);
    }

    private function getPrivacyPageUrl(): string
    {
        $pageId = get_option('wp_page_for_privacy_policy');
        if (!$pageId || get_post_status($pageId) !== 'publish') {
            return '';
        }

        if ($this->i18n->hasWpml()) {
            $pageId = apply_filters('wpml_object_id', $pageId, 'page', true);
        }

        return get_permalink($pageId) ?: '';
    }
}
