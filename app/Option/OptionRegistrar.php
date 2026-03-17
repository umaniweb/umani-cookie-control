<?php

declare(strict_types=1);

namespace UMANI\Option;

use UMANI\Field\FieldRenderer;
use UMANI\I18n\I18nService;
use UMANI\Section\SettingsSection;

class OptionRegistrar
{
    private string $slug;
    private I18nService $i18n;
    private FieldRenderer $renderer;

    public function __construct(string $slug, I18nService $i18n)
    {
        $this->slug = $slug;
        $this->i18n = $i18n;
        $this->renderer = new FieldRenderer();
    }

    public function register(array $config): void
    {
        foreach ($config as $sectionKey => $section) {
            $page = $this->slug . '-' . $section['page'];
            $sectionId = $this->slug . '-' . $sectionKey . '-section';

            add_settings_section(
                $sectionId,
                $section['section_title'],
                '__return_false',
                $page,
                $section['section_args'] ?? []
            );

            foreach ($section['fields'] as $fieldKey => $field) {
                $this->registerField($fieldKey, $field, $page, $sectionId);
            }
        }
    }

    private function registerField(string $fieldKey, array $field, string $page, string $sectionId): void
    {
        $isI18n = !empty($field['i18n']);

        if ($isI18n && $this->i18n->hasWpml()) {
            $this->registerI18nFields($fieldKey, $field, $page, $sectionId);
        } else {
            $this->registerSingleField($fieldKey, $field, $page, $sectionId);
        }
    }

    private function registerSingleField(string $fieldKey, array $field, string $page, string $sectionId): void
    {
        $optionName = $this->slug . '-' . $fieldKey;
        $default = $field['default'] ?? null;

        if ($default === null && !empty($field['defaults'])) {
            $siteName = get_bloginfo('name');
            $lang = $this->i18n->getSiteLanguage();
            $default = str_replace('{site_name}', $siteName, $this->i18n->getDefault($field['defaults'], $lang));
        }

        $args = [
            'type'              => $field['type'],
            'sanitize_callback' => $field['sanitize'] ?? null,
        ];

        if ($default !== null) {
            $args['default'] = $default;
        }

        register_setting($page, $optionName, $args);

        add_settings_field(
            $optionName,
            $field['label'],
            [$this->renderer, 'render'],
            $page,
            $sectionId,
            [
                'label_for'  => $optionName,
                'render'     => $field['render'],
                'input_type' => $field['input_type'] ?? 'text',
            ]
        );
    }

    private function registerI18nFields(string $fieldKey, array $field, string $page, string $sectionId): void
    {
        $languages = $this->i18n->getActiveLanguages();
        $currentLang = $this->i18n->getCurrentLanguage();
        $siteName = get_bloginfo('name');

        foreach ($languages as $langCode) {
            $optionName = $this->slug . '-' . $fieldKey . '-' . $langCode;
            $default = str_replace(
                '{site_name}',
                $siteName,
                $this->i18n->getDefault($field['defaults'] ?? [], $langCode)
            );

            $args = [
                'type'              => $field['type'],
                'sanitize_callback' => $field['sanitize'] ?? null,
                'default'           => $default,
            ];

            register_setting($page, $optionName, $args);

            $isCurrentLang = ($langCode === $currentLang);

            add_settings_field(
                $optionName,
                $isCurrentLang ? $field['label'] : '',
                [$this->renderer, 'render'],
                $page,
                $sectionId,
                [
                    'label_for'  => $optionName,
                    'render'     => $isCurrentLang ? $field['render'] : 'input',
                    'input_type' => $isCurrentLang ? ($field['input_type'] ?? 'text') : 'hidden',
                ]
            );
        }
    }
}
