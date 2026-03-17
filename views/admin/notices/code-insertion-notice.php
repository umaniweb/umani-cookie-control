<?php defined('ABSPATH') || exit; ?>

<div class="notice notice-error">
    <h3>
        <strong style="color:red;">L'ajout du code GTM dans la balise &lt;head&gt; est requis pour activer la bannière de cookies.</strong>
    </h3>
</div>

<div class="notice notice-info">
    <p><strong style="color:red;"><u>Attention :</u> Avant d'ajouter le code GTM, il est fortement conseillé de retirer tout code existant de votre site pour éviter les conflits.</strong></p>
    <p><b>Ce plugin nécessite une bonne compréhension de votre tableau de bord <a href="https://tagmanager.google.com/" target="_blank" rel="noopener">Google Tag Manager</a>.</b></p>
    <p>
        <strong><u>Fonctionnement :</u></strong><br>
        Lors du chargement de la page, les consentements pour les types <em>ad_storage</em>, <em>ad_user_data</em>, <em>ad_personalization</em>,
        <em>analytics_storage</em> et <em>personalization_storage</em> sont initialement définis sur <em>denied</em>.
        <em>security_storage</em> et <em>functionality_storage</em> sont toujours <em>granted</em> (cookies nécessaires).<br>
        L'utilisateur peut accepter tout, refuser tout, ou paramétrer ses préférences par catégorie (nécessaires, analytiques, marketing).<br>
        Pour plus d'informations, consultez la <a href="https://developers.google.com/tag-platform/devguides/consent" target="_blank" rel="noopener">documentation Google Consent Mode v2</a>.
    </p>
    <p>Lorsque l'utilisateur accepte, un cookie d'une durée de 1 an est créé. En cas de refus, le cookie dure 24 heures.</p>
</div>
