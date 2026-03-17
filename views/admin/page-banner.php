<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php do_action('admin_notices'); ?>
    <?php
    $privacyPage = get_option('wp_page_for_privacy_policy');
    $hasHead = get_option($slug . '-head');

    if ($privacyPage && get_post_status($privacyPage) === 'publish' && $hasHead) :
        $this->renderForm('banner');
        $this->renderResetForm('banner');
    endif;
    ?>
</div>
