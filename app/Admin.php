<?php

declare(strict_types=1);

namespace UMANI;

class Admin
{
    private string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public function addMenus(): void
    {
        add_menu_page(
            'Umani Cookie Control',
            'Cookie Consent',
            'manage_options',
            $this->slug . '-code-insertion',
            [$this, 'renderCodeInsertionPage'],
            'dashicons-privacy',
            100
        );

        add_submenu_page(
            $this->slug . '-code-insertion',
            'Code Insertion',
            'Code Insertion',
            'manage_options',
            $this->slug . '-code-insertion',
            [$this, 'renderCodeInsertionPage']
        );

        add_submenu_page(
            $this->slug . '-code-insertion',
            'Consent Banner',
            'Consent Banner',
            'manage_options',
            $this->slug . '-banner',
            [$this, 'renderBannerPage']
        );

        add_submenu_page(
            $this->slug . '-code-insertion',
            'Updater Settings',
            'Mise à jour',
            'manage_options',
            $this->slug . '-updater-settings',
            [$this, 'renderUpdaterPage']
        );
    }

    public function renderCodeInsertionPage(): void
    {
        $this->addCodeInsertionNotices();
        include UMANI_CC_DIR . '/views/admin/page-code-insertion.php';
    }

    public function renderBannerPage(): void
    {
        $this->addBannerNotices();
        $slug = $this->slug;
        include UMANI_CC_DIR . '/views/admin/page-banner.php';
    }

    public function renderUpdaterPage(): void
    {
        include UMANI_CC_DIR . '/views/admin/page-updater.php';
    }

    public function renderForm(string $formName): void
    {
        $menuSlug = $this->slug . '-' . $formName;
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields($menuSlug);
            do_settings_sections($menuSlug);
            submit_button();
            ?>
        </form>
        <?php
    }

    public function renderResetForm(string $formName): void
    {
        $menuSlug = $this->slug . '-' . $formName;
        ?>
        <form method="post" action="options.php" style="display:inline;">
            <?php settings_fields($menuSlug); ?>
            <?php foreach (get_registered_settings() as $key => $setting) : ?>
                <?php if (($setting['group'] ?? '') === $menuSlug) : ?>
                    <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($setting['default'] ?? ''); ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            <p>
                <?php submit_button(
                    __('Réinitialiser', 'umani-cookie-control'),
                    'secondary',
                    'submit',
                    false
                ); ?>
                <?php esc_html_e('Remettre les paramètres et textes par défaut pour toutes les langues', 'umani-cookie-control'); ?>
            </p>
        </form>
        <?php
    }

    private function addCodeInsertionNotices(): void
    {
        add_action('admin_notices', function () {
            include UMANI_CC_DIR . '/views/admin/notices/code-insertion-notice.php';
        });
    }

    private function addBannerNotices(): void
    {
        add_action('admin_notices', function () {
            include UMANI_CC_DIR . '/views/admin/notices/banner-notice.php';
        });
    }
}
