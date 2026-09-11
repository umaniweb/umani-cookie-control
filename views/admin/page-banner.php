<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php do_action('admin_notices'); ?>
    <?php if ($state['hasPrivacy'] && $state['hasTracking']) : ?>
        <?php $this->renderForm('banner'); ?>
        <?php $this->renderResetForm('banner'); ?>
    <?php endif; ?>
</div>
