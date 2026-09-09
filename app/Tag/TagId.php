<?php

declare(strict_types=1);

namespace UMANI\Tag;

class TagId
{
    public static function sanitize(mixed $value): string
    {
        $id = strtoupper(trim(sanitize_text_field((string) $value)));

        if ($id === '' || self::type($id) !== null) {
            return $id;
        }

        add_settings_error(
            UMANI_CC_SLUG . '-tag-id',
            'umani-cc-invalid-tag-id',
            'ID invalide : le format attendu est GTM-XXXXXXX (Tag Manager) ou G-XXXXXXXXXX (Analytics 4).'
        );

        return (string) get_option(UMANI_CC_SLUG . '-tag-id', '');
    }

    public static function type(string $id): ?string
    {
        if (preg_match('/^GTM-[A-Z0-9]+$/', $id)) {
            return 'gtm';
        }

        if (preg_match('/^G-[A-Z0-9]+$/', $id)) {
            return 'ga4';
        }

        return null;
    }
}
