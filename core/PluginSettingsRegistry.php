<?php

declare(strict_types=1);

namespace Core;

class PluginSettingsRegistry
{
    private static array $schemas = [];

    public static function register(string $plugin, array $schema): void
    {
        self::validatePluginName($plugin);
        if (!isset($schema['fields']) || !is_array($schema['fields'])) {
            throw new \InvalidArgumentException("Missing 'fields' key for plugin '{$plugin}'");
        }

        $normalized = [
            'title' => $schema['title'] ?? ucfirst($plugin) . ' Settings',
            'fields' => [],
        ];

        foreach ($schema['fields'] as $key => $field) {
            $normalized['fields'][$key] = array_merge([
                'type' => 'text',
                'label' => ucfirst(str_replace('_', ' ', $key)),
                'default' => '',
                'required' => false,
                'maxlength' => null,
                'minlength' => null,
                'min' => null,
                'max' => null,
                'rows' => 4,
                'sanitize' => $field['sanitize'] ?? (in_array($field['type'] ?? '', ['bool', 'boolean', 'checkbox'], true) ? 'bool' : 'text'),
                'description' => '',
            ], $field);
        }

        self::$schemas[$plugin] = $normalized;
    }

    public static function has(string $plugin): bool
    {
        return isset(self::$schemas[$plugin]);
    }

    public static function get(string $plugin): array
    {
        return self::$schemas[$plugin] ?? [];
    }

    public static function getAll(): array
    {
        return self::$schemas;
    }

    public static function validate(array $input, string $plugin): array
    {
        $schema = self::get($plugin);
        if (empty($schema)) {
            return ['valid' => false, 'errors' => ['Plugin not found'], 'data' => []];
        }

        $errors = [];
        $data = [];

        foreach ($schema['fields'] as $key => $field) {
            $value = $input[$key] ?? null;
            $isRequired = $field['required'] ?? false;
            $isEmpty = $value === null || $value === '' || (is_array($value) && empty($value));

            // Required check
            if ($isRequired && $isEmpty) {
                $errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' is required.';
                // Preserve the submitted value for repopulation (sanitized)
                $data[$key] = self::sanitizeValue($value, $field['sanitize']);
                continue;
            }

            // If not required and empty, use default
            if ($isEmpty) {
                $data[$key] = $field['default'] ?? '';
                continue;
            }

            // Sanitize
            $sanitized = self::sanitizeValue($value, $field['sanitize']);

            // Server-side maxlength enforcement
            if ($field['maxlength'] !== null && strlen((string) $sanitized) > (int) $field['maxlength']) {
                $errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must not exceed ' . $field['maxlength'] . ' characters.';
                $data[$key] = $sanitized;
                continue;
            }

            // Minlength
            if ($field['minlength'] !== null && strlen((string) $sanitized) < (int) $field['minlength'] && !empty($sanitized)) {
                $errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be at least ' . $field['minlength'] . ' characters.';
                $data[$key] = $sanitized;
                continue;
            }

            // Min / Max for integers
            if ($field['sanitize'] === 'int') {
                $intValue = (int) $sanitized;
                if ($field['min'] !== null && $intValue < (int) $field['min']) {
                    $errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be at least ' . $field['min'] . '.';
                    $data[$key] = $sanitized;
                    continue;
                }
                if ($field['max'] !== null && $intValue > (int) $field['max']) {
                    $errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must not exceed ' . $field['max'] . '.';
                    $data[$key] = $sanitized;
                    continue;
                }
            }

            // Select option validation
            if ($field['type'] === 'select' && !empty($field['options'])) {
                if (!array_key_exists($sanitized, $field['options']) && !array_key_exists((string) $sanitized, $field['options'])) {
                    $errors[$key] = 'Invalid option selected.';
                    $data[$key] = $field['default'] ?? '';
                    continue;
                }
            }

            // Email validation
            if ($field['sanitize'] === 'email' && !empty($sanitized)) {
                if (!filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
                    $errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be a valid email address.';
                    $data[$key] = $sanitized;
                    continue;
                }
            }

            // URL validation
            if ($field['sanitize'] === 'url' && !empty($sanitized)) {
                $url = trim($sanitized);
                if (!filter_var($url, FILTER_VALIDATE_URL) ||
                    !in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
                    $errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be a valid HTTP/HTTPS URL.';
                    $data[$key] = $sanitized;
                    continue;
                }
            }

            $data[$key] = $sanitized;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $data,
        ];
    }

    private static function sanitizeValue(mixed $value, string $type): mixed
    {
        switch ($type) {
            case 'int':
                return (int) $value;
            case 'bool':
            case 'boolean':
                if (is_string($value)) {
                    return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
                }
                return (bool) $value;
            case 'email':
                return filter_var(trim((string) $value), FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var(trim((string) $value), FILTER_SANITIZE_URL);
            case 'textarea':
            case 'text':
            default:
                return trim((string) $value);
        }
    }

    private static function validatePluginName(string $plugin): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $plugin)) {
            throw new \InvalidArgumentException("Invalid plugin identifier: {$plugin}");
        }
    }
}
