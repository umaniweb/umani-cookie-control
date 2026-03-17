<?php

declare(strict_types=1);

namespace UMANI\I18n;

class I18nService
{
    public function getActiveLanguages(): array
    {
        $languages = apply_filters('wpml_active_languages', null);

        if (is_array($languages)) {
            return array_column($languages, 'code', 'code');
        }

        return [];
    }

    public function getCurrentLanguage(): string
    {
        $lang = apply_filters('wpml_current_language', null);

        return is_string($lang) ? $lang : $this->getSiteLanguage();
    }

    public function getSiteLanguage(): string
    {
        return explode('_', get_locale())[0];
    }

    public function hasWpml(): bool
    {
        return !empty($this->getActiveLanguages());
    }

    public function getDefault(array $defaults, string $langCode): string
    {
        return $defaults[$langCode]
            ?? $defaults['fr']
            ?? reset($defaults)
            ?: '';
    }
}
