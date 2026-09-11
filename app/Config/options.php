<?php

declare(strict_types=1);

return [
    'google-tag' => [
        'section_title'       => 'Tag Google',
        'section_description' => 'Identifiant du conteneur Tag Manager ou de la propriété Analytics. Le snippet correspondant est généré et inséré automatiquement, juste après l\'initialisation du Consent Mode v2.',
        'page'                => 'code-insertion',
        'fields'              => [
            'tag-id' => [
                'type'        => 'string',
                'render'      => 'input',
                'input_type'  => 'text',
                'label'       => 'Identifiant du tag',
                'description' => 'Format <code>GTM-XXXXXXX</code> pour Tag Manager, <code>G-XXXXXXXXXX</code> pour Analytics 4. Laissez vide pour coller vous-même votre snippet dans le champ &lt;head&gt; ci-dessous.',
                'sanitize'    => ['UMANI\\Tag\\TagId', 'sanitize'],
            ],
        ],
    ],
    'code-insertion' => [
        'section_title'       => 'Code personnalisé',
        'section_description' => 'Pour tout code non couvert par le champ ci-dessus : autre solution de mesure, pixel publicitaire, balise de vérification de domaine.',
        'page'                => 'code-insertion',
        'fields'              => [
            'head' => [
                'type'        => 'string',
                'render'      => 'codeEditor',
                'label'       => 'Balise &lt;head&gt;',
                'description' => 'Inséré en début de &lt;head&gt;, après le Consent Mode v2 et le tag Google.',
                'sanitize'    => null,
            ],
            'body' => [
                'type'        => 'string',
                'render'      => 'codeEditor',
                'label'       => 'Balise &lt;body&gt;',
                'description' => 'Inséré à l\'ouverture de &lt;body&gt;, ou en pied de page si le thème ne déclare pas <code>wp_body_open</code>.',
                'sanitize'    => null,
            ],
        ],
    ],
    'banner' => [
        'section_title' => '',
        'page'          => 'banner',
        'fields'        => [
            'banner-active' => [
                'type'        => 'integer',
                'render'      => 'checkbox',
                'label'       => 'Activer la bannière',
                'description' => 'La bannière s\'affiche en façade uniquement si une page de politique de confidentialité est publiée dans <strong>Réglages &rsaquo; Vie privée</strong>.',
                'sanitize'    => 'absint',
            ],
        ],
    ],
    'banner-text' => [
        'section_title' => '',
        'page'          => 'banner',
        'section_args'  => [
            'before_section' => '<section class="%s">',
            'after_section'  => '</section>',
            'section_class'  => 'banner-active',
        ],
        'fields' => [
            'banner-text' => [
                'type'     => 'string',
                'render'   => 'textarea',
                'label'    => 'Texte de la bannière',
                'sanitize' => 'sanitize_textarea_field',
                'i18n'     => true,
                'defaults' => [
                    'fr' => '{site_name} utilise des cookies pour vous offrir une expérience plus fluide, adapter les contenus à vos préférences et analyser les performances générales. En poursuivant votre navigation, vous acceptez l\'utilisation de ces technologies.',
                    'en' => '{site_name} uses cookies to offer you a smoother experience, tailor content to your preferences, and analyze overall performance. By continuing to browse, you accept the use of these technologies.',
                    'es' => '{site_name} utiliza cookies para ofrecerte una experiencia más fluida, adaptar los contenidos a tus preferencias y analizar el rendimiento general. Al continuar navegando, aceptas el uso de estas tecnologías.',
                ],
            ],
        ],
    ],
    'banner-buttons' => [
        'section_title' => 'Textes des boutons',
        'page'          => 'banner',
        'section_args'  => [
            'before_section' => '<section class="%s">',
            'after_section'  => '</section>',
            'section_class'  => 'banner-active',
        ],
        'fields' => [
            'banner-readMore-text' => [
                'type'     => 'string',
                'render'   => 'input',
                'input_type' => 'text',
                'label'    => 'Politique',
                'sanitize' => 'sanitize_text_field',
                'i18n'     => true,
                'defaults' => [
                    'fr' => 'Politique de confidentialité',
                    'en' => 'Read more',
                    'es' => 'Leer más',
                ],
            ],
            'banner-accept-text' => [
                'type'     => 'string',
                'render'   => 'input',
                'input_type' => 'text',
                'label'    => 'Accepter tout',
                'sanitize' => 'sanitize_text_field',
                'i18n'     => true,
                'defaults' => [
                    'fr' => 'Accepter tout',
                    'en' => 'Accept all',
                    'es' => 'Aceptar todo',
                ],
            ],
            'banner-reject-text' => [
                'type'     => 'string',
                'render'   => 'input',
                'input_type' => 'text',
                'label'    => 'Refuser',
                'sanitize' => 'sanitize_text_field',
                'i18n'     => true,
                'defaults' => [
                    'fr' => 'Refuser',
                    'en' => 'Reject',
                    'es' => 'Rechazar',
                ],
            ],
            'banner-settings-text' => [
                'type'     => 'string',
                'render'   => 'input',
                'input_type' => 'text',
                'label'    => 'Paramétrer',
                'sanitize' => 'sanitize_text_field',
                'i18n'     => true,
                'defaults' => [
                    'fr' => 'Paramétrer',
                    'en' => 'Customize',
                    'es' => 'Configurar',
                ],
            ],
            'banner-save-text' => [
                'type'     => 'string',
                'render'   => 'input',
                'input_type' => 'text',
                'label'    => 'Sauvegarder les préférences',
                'sanitize' => 'sanitize_text_field',
                'i18n'     => true,
                'defaults' => [
                    'fr' => 'Sauvegarder',
                    'en' => 'Save preferences',
                    'es' => 'Guardar preferencias',
                ],
            ],
        ],
    ],
    'banner-colors' => [
        'section_title' => 'Couleurs de la bannière',
        'page'          => 'banner',
        'section_args'  => [
            'before_section' => '<section class="%s">',
            'after_section'  => '</section>',
            'section_class'  => 'banner-active',
        ],
        'fields' => [
            'banner-color' => [
                'type'     => 'string',
                'render'   => 'colorPicker',
                'label'    => 'Couleur de fond',
                'sanitize' => 'sanitize_hex_color',
                'default'  => '#FFFFFF',
            ],
            'banner-text-color' => [
                'type'     => 'string',
                'render'   => 'colorPicker',
                'label'    => 'Couleur du texte',
                'sanitize' => 'sanitize_hex_color',
                'default'  => '#333333',
            ],
            'banner-link-color' => [
                'type'     => 'string',
                'render'   => 'colorPicker',
                'label'    => 'Couleur du lien',
                'sanitize' => 'sanitize_hex_color',
                'default'  => '#0066CC',
            ],
            'banner-button-color' => [
                'type'     => 'string',
                'render'   => 'colorPicker',
                'label'    => 'Couleur de fond du bouton',
                'sanitize' => 'sanitize_hex_color',
                'default'  => '#222222',
            ],
            'banner-button-text-color' => [
                'type'     => 'string',
                'render'   => 'colorPicker',
                'label'    => 'Couleur du texte du bouton',
                'sanitize' => 'sanitize_hex_color',
                'default'  => '#FFFFFF',
            ],
        ],
    ],
    'updater-settings' => [
        'section_title'       => 'Connexion GitHub',
        'section_description' => 'Identifiants utilisés pour vérifier la présence d\'une nouvelle version du plugin et la télécharger. Sans ces informations, aucune mise à jour automatique n\'est proposée.',
        'page'                => 'updater-settings',
        'fields'              => [
            'username' => [
                'type'       => 'string',
                'render'     => 'input',
                'input_type' => 'text',
                'label'      => 'Nom d\'utilisateur GitHub',
                'sanitize'   => 'sanitize_text_field',
            ],
            'token' => [
                'type'        => 'string',
                'render'      => 'input',
                'input_type'  => 'password',
                'label'       => 'Token d\'accès GitHub',
                'description' => 'Token personnel disposant de l\'accès en lecture au dépôt du plugin.',
                'sanitize'    => 'sanitize_text_field',
            ],
            'server' => [
                'type'       => 'string',
                'render'     => 'input',
                'input_type' => 'url',
                'label'      => 'URL du serveur',
                'sanitize'   => 'sanitize_url',
            ],
        ],
    ],
];
