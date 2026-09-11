<?php defined('ABSPATH') || exit; ?>

<?php if (empty($state['hasTracking'])) : ?>
    <div class="notice notice-warning">
        <p>
            <strong>Aucun tag n'est configuré.</strong>
            Renseignez un identifiant Tag Manager ou Analytics ci-dessous, ou collez votre propre snippet dans le champ <code>&lt;head&gt;</code>.
            Sans tag, la bannière n'a rien à piloter.
        </p>
    </div>
<?php endif; ?>

<div class="notice notice-info">
    <p>
        <strong>Avant de configurer un tag</strong>, retirez du site tout code Tag Manager ou Analytics déjà en place : thème, autre extension, script inséré à la main.
        Deux chargements simultanés faussent la mesure.
    </p>
    <p>
        <strong>Consent Mode v2</strong> : au chargement de la page, <code>ad_storage</code>, <code>ad_user_data</code>, <code>ad_personalization</code>,
        <code>analytics_storage</code> et <code>personalization_storage</code> sont sur <code>denied</code>.
        <code>security_storage</code> et <code>functionality_storage</code> restent sur <code>granted</code>, ce sont les cookies nécessaires.
        Le choix du visiteur met ces valeurs à jour sans rechargement de page.
    </p>
    <p>
        Le consentement est conservé 1 an en cas d'acceptation, 24 heures en cas de refus.
        <a href="https://developers.google.com/tag-platform/devguides/consent" target="_blank" rel="noopener">Documentation Consent Mode v2</a>
        &middot;
        <a href="https://tagmanager.google.com/" target="_blank" rel="noopener">Google Tag Manager</a>
    </p>
</div>
