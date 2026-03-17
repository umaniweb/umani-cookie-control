<?php

declare(strict_types=1);

namespace UMANI\Field;

class FieldRenderer
{
    public function render(array $args): void
    {
        $method = match ($args['render']) {
            'input'       => 'renderInput',
            'textarea'    => 'renderTextarea',
            'checkbox'    => 'renderCheckbox',
            'colorPicker' => 'renderColorPicker',
            'codeEditor'  => 'renderCodeEditor',
            default       => 'renderInput',
        };

        $this->$method($args);
    }

    private function renderInput(array $args): void
    {
        $field = $args['label_for'];
        $type = $args['input_type'] ?? 'text';
        $value = get_option($field);

        printf(
            '<input type="%s" name="%s" id="%s" value="%s" class="regular-text">',
            esc_attr($type),
            esc_attr($field),
            esc_attr($field),
            esc_attr((string) $value)
        );
    }

    private function renderTextarea(array $args): void
    {
        $field = $args['label_for'];
        $value = get_option($field);

        printf(
            '<textarea name="%s" id="%s" style="width:50%%;height:100px;">%s</textarea>',
            esc_attr($field),
            esc_attr($field),
            esc_textarea((string) $value)
        );
    }

    private function renderCheckbox(array $args): void
    {
        $field = $args['label_for'];
        $value = get_option($field);

        printf(
            '<input type="checkbox" name="%s" id="%s" value="1" %s>',
            esc_attr($field),
            esc_attr($field),
            checked(1, $value, false)
        );
    }

    private function renderColorPicker(array $args): void
    {
        $field = $args['label_for'];
        $value = get_option($field);

        printf(
            '<input type="text" name="%s" id="%s" value="%s" class="umani-color-picker">',
            esc_attr($field),
            esc_attr($field),
            esc_attr(sanitize_hex_color((string) $value) ?: '')
        );
    }

    private function renderCodeEditor(array $args): void
    {
        $field = $args['label_for'];
        $value = get_option($field);

        printf(
            '<textarea name="%s" id="%s" class="umani-code-editor" style="width:100%%;height:200px;">%s</textarea>',
            esc_attr($field),
            esc_attr($field),
            esc_textarea((string) $value)
        );
    }
}
