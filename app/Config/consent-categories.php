<?php

declare(strict_types=1);

return [
    'necessary' => [
        'label' => [
            'fr' => 'Nécessaires',
            'en' => 'Necessary',
            'es' => 'Necesarias',
        ],
        'description' => [
            'fr' => 'Essentiels au fonctionnement du site.',
            'en' => 'Essential for the website to function.',
            'es' => 'Esenciales para el funcionamiento del sitio.',
        ],
        'required'  => true,
        'gtm_types' => ['security_storage', 'functionality_storage'],
    ],
    'analytics' => [
        'label' => [
            'fr' => 'Analytiques',
            'en' => 'Analytics',
            'es' => 'Analíticas',
        ],
        'description' => [
            'fr' => 'Nous aident à comprendre comment vous utilisez le site.',
            'en' => 'Help us understand how you use the site.',
            'es' => 'Nos ayudan a entender cómo utiliza el sitio.',
        ],
        'required'  => false,
        'gtm_types' => ['analytics_storage', 'personalization_storage'],
    ],
    'marketing' => [
        'label' => [
            'fr' => 'Marketing',
            'en' => 'Marketing',
            'es' => 'Marketing',
        ],
        'description' => [
            'fr' => 'Utilisés pour vous proposer des publicités pertinentes.',
            'en' => 'Used to show you relevant advertisements.',
            'es' => 'Utilizadas para mostrarle anuncios relevantes.',
        ],
        'required'  => false,
        'gtm_types' => ['ad_storage', 'ad_user_data', 'ad_personalization'],
    ],
];
