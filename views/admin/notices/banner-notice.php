<?php defined('ABSPATH') || exit; ?>

<?php if (empty($state['hasPrivacy']) || empty($state['hasTracking'])) : ?>
    <div class="notice notice-warning">
        <p><strong>La bannière ne peut pas encore être configurée.</strong> Il manque :</p>
        <ul class="ul-disc">
            <?php if (empty($state['hasTracking'])) : ?>
                <li>
                    un tag à piloter, à renseigner dans
                    <a href="<?php echo esc_url(admin_url('admin.php?page=' . UMANI_CC_SLUG . '-code-insertion')); ?>">Tags et code</a>
                </li>
            <?php endif; ?>
            <?php if (empty($state['hasPrivacy'])) : ?>
                <li>
                    une page de politique de confidentialité publiée, à définir dans
                    <a href="<?php echo esc_url(admin_url('options-privacy.php')); ?>">Réglages &rsaquo; Vie privée</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="notice notice-info">
    <p><strong>Traduction des textes</strong></p>
    <p>
        <a href="https://wpml.org/" target="_blank" rel="noopener">WPML</a> est nécessaire pour traduire les textes de la bannière.
        Les champs affichés correspondent à la langue courante de l'administration : utilisez le sélecteur de langue WPML pour saisir les autres traductions.
    </p>
</div>
