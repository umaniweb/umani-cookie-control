<?php defined('ABSPATH') || exit; ?>

<div id="umani-cc-wrap" aria-label="<?php esc_attr_e('Bannière de consentement aux cookies', 'umani-cookie-control'); ?>" role="dialog">
    <div id="umani-cc-banner">

        <div id="umani-cc-main">
            <div class="umani-cc-text">
                <?php echo esc_html($bannerText); ?>
                <?php if ($privacyPageUrl) : ?>
                    <a class="umani-cc-link" href="<?php echo esc_url($privacyPageUrl); ?>" target="_blank" rel="noopener"><?php echo esc_html($textReadMore); ?></a>
                <?php endif; ?>
            </div>

            <div class="umani-cc-actions">
                <button type="button" class="umani-cc-btn umani-cc-btn--secondary" id="umani-cc-reject"><?php echo esc_html($textReject); ?></button>
                <button type="button" class="umani-cc-btn umani-cc-btn--secondary" id="umani-cc-settings-toggle"><?php echo esc_html($textSettings); ?></button>
                <button type="button" class="umani-cc-btn umani-cc-btn--primary" id="umani-cc-accept"><?php echo esc_html($textAccept); ?></button>
            </div>
        </div>

        <div id="umani-cc-preferences" hidden>
            <div class="umani-cc-categories">
                <?php foreach ($categories as $key => $cat) : ?>
                    <label class="umani-cc-category">
                        <span class="umani-cc-category-header">
                            <input
                                type="checkbox"
                                class="umani-cc-toggle"
                                data-category="<?php echo esc_attr($key); ?>"
                                <?php echo $cat['required'] ? 'checked disabled' : ''; ?>
                            >
                            <span class="umani-cc-category-name"><?php echo esc_html($cat['label']); ?></span>
                        </span>
                        <span class="umani-cc-category-desc"><?php echo esc_html($cat['description']); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="umani-cc-actions">
                <button type="button" class="umani-cc-btn umani-cc-btn--primary" id="umani-cc-save"><?php echo esc_html($textSave); ?></button>
            </div>
        </div>

    </div>
</div>
