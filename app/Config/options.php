<?php

declare(strict_types=1);

return [
    'code-insertion' => [
        'section_title' => 'Code GTM<br><small>Il est possible d\'ajouter du code qui n\'a pas de rapport avec GTM.</small>',
        'page'          => 'code-insertion',
        'fields'        => [
            'head' => [
                'type'     => 'string',
                'render'   => 'codeEditor',
                'label'    => 'Code HTML à insérer dans le &lt;head&gt;',
                'sanitize' => null,
            ],
            'body' => [
                'type'     => 'string',
                'render'   => 'codeEditor',
                'label'    => 'Code HTML à insérer dans le &lt;body&gt;',
                'sanitize' => null,
            ],
        ],
    ],
    'banner' => [
        'section_title' => '',
        'page'          => 'banner',
        'fields'        => [
            'banner-active' => [
                'type'     => 'integer',
                'render'   => 'checkbox',
                'label'    => 'Activer la bannière',
                'sanitize' => 'absint',
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
        'section_title' => '',
        'page'          => 'updater-settings',
        'fields'        => [
            'token' => [
                'type'       => 'string',
                'render'     => 'input',
                'input_type' => 'password',
                'label'      => 'Token d\'accès GitHub',
                'sanitize'   => 'sanitize_text_field',
            ],
            'username' => [
                'type'       => 'string',
                'render'     => 'input',
                'input_type' => 'text',
                'label'      => 'Nom d\'utilisateur GitHub',
                'sanitize'   => 'sanitize_text_field',
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
