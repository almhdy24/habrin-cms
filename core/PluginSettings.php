<?php

declare(strict_types=1);

namespace Core;

class PluginSettings
{
    private static array $cache = [];
    private static ?string $baseDir = null;
    private static bool $initialized = false;

    private static function ensureInitialized(): void
    {
        if (self::$initialized) return;
        self::$baseDir = App::path('content', 'plugin-settings');
        if (!is_dir(self::$baseDir)) {
            if (!mkdir(self::$baseDir, 0755, true)) {
                throw new \RuntimeException("Unable to create plugin settings directory: " . self::$baseDir);
            }
        }
        self::$initialized = true;
    }

    public static function init(): void
    {
        self::ensureInitialized();
    }

    public static function register(string $plugin, array $schema): void
    {
        PluginSettingsRegistry::register($plugin, $schema);
    }

    public static function getEffective(string $plugin): array
    {
        self::ensureInitialized();
        $saved = self::getAll($plugin);
        $schema = PluginSettingsRegistry::get($plugin);

        $defaults = [];
        foreach ($schema['fields'] ?? [] as $key => $field) {
            $defaults[$key] = $field['default'] ?? null;
        }

        return array_replace($defaults, $saved);
    }

    public static function get(string $plugin, string $key, mixed $default = null): mixed
    {
        $settings = self::getEffective($plugin);
        return $settings[$key] ?? $default;
    }

    public static function getAll(string $plugin): array
    {
        self::ensureInitialized();
        if (isset(self::$cache[$plugin])) {
            return self::$cache[$plugin];
        }

        $file = self::getFile($plugin);
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                self::$cache[$plugin] = $data;
                return $data;
            }
        }
        self::$cache[$plugin] = [];
        return [];
    }

    public static function save(string $plugin, array $settings): array
    {
        self::ensureInitialized();
        $result = PluginSettingsRegistry::validate($settings, $plugin);

        if (!$result['valid']) {
            return $result;
        }

        $file = self::getFile($plugin);
        $temp = $file . '.tmp';
        $json = json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return ['valid' => false, 'errors' => ['Failed to encode JSON'], 'data' => []];
        }
        if (file_put_contents($temp, $json, LOCK_EX) === false) {
            return ['valid' => false, 'errors' => ['Failed to write file'], 'data' => []];
        }
        if (!rename($temp, $file)) {
            @unlink($temp);
            return ['valid' => false, 'errors' => ['Failed to replace file'], 'data' => []];
        }

        self::$cache[$plugin] = $result['data'];
        return $result;
    }

    public static function renderForm(string $plugin, ?array $current = null, array $errors = []): void
    {
        self::ensureInitialized();
        if (!PluginSettingsRegistry::has($plugin)) {
            echo '<div class="notification notification-danger">No settings registered for this plugin.</div>';
            return;
        }

        $schema = PluginSettingsRegistry::get($plugin);
        if ($current === null) {
            $current = self::getEffective($plugin);
        }

        $hasErrors = !empty($errors);

        echo '<form method="POST" action="/admin/plugins/settings/' . htmlspecialchars($plugin) . '" class="plugin-settings-form">';
        echo '<input type="hidden" name="csrf_token" value="' . Session::csrfToken() . '">';

        // Show general error message if any fields have errors
        if ($hasErrors) {
            echo '<div class="notification notification-danger" style="margin-bottom:1.5rem;">';
            echo '<i data-lucide="alert-circle"></i> ';
            echo 'Please fix the errors below.';
            echo '</div>';
        }

        // Group fields
        foreach ($schema['fields'] as $key => $field) {
            $value = $current[$key] ?? $field['default'] ?? '';
            // Ensure value is string for escaping
            $displayValue = is_scalar($value) ? (string) $value : '';
            $id = 'plugin_' . $plugin . '_' . $key;
            $hasError = isset($errors[$key]);

            echo '<div class="field ' . ($hasError ? 'has-error' : '') . '" style="margin-bottom:1.25rem;">';

            // Label with icon and required indicator
            echo '<label class="label" for="' . $id . '">';
            echo '<i data-lucide="' . self::getIcon($field['type']) . '"></i> ';
            echo htmlspecialchars($field['label']);
            if ($field['required']) {
                echo ' <span class="text-muted" style="font-weight:400;">(required)</span>';
            }
            echo '</label>';

            // Error message
            if ($hasError) {
                echo '<div class="help" style="color:var(--danger);margin-bottom:0.3rem;">';
                echo '<i data-lucide="alert-circle"></i> ' . htmlspecialchars($errors[$key]);
                echo '</div>';
            }

            // Field input
            switch ($field['type']) {
                case 'textarea':
                    echo '<textarea class="textarea" id="' . $id . '" name="' . htmlspecialchars($key) . '" rows="' . (int) ($field['rows'] ?? 4) . '"';
                    if ($field['maxlength']) echo ' maxlength="' . (int) $field['maxlength'] . '"';
                    if ($field['required']) echo ' required';
                    echo '>' . htmlspecialchars($displayValue) . '</textarea>';
                    break;

                case 'select':
                    echo '<select class="input" id="' . $id . '" name="' . htmlspecialchars($key) . '"';
                    if ($field['required']) echo ' required';
                    echo '>';
                    foreach ($field['options'] as $optValue => $optLabel) {
                        $selected = ((string) $optValue === $displayValue) ? ' selected' : '';
                        echo '<option value="' . htmlspecialchars((string) $optValue) . '"' . $selected . '>' . htmlspecialchars($optLabel) . '</option>';
                    }
                    echo '</select>';
                    break;

                case 'checkbox':
                    $checked = $value ? ' checked' : '';
                    echo '<div style="margin-top:0.3rem;display:flex;align-items:center;gap:0.5rem;">';
                    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="0">';
                    echo '<input type="checkbox" id="' . $id . '" name="' . htmlspecialchars($key) . '" value="1"' . $checked . ' style="width:18px;height:18px;">';
                    echo '<span style="font-size:0.9rem;color:var(--text-light);">Enabled</span>';
                    echo '</div>';
                    break;

                default: // text, number, email, url
                    $inputType = 'text';
                    if ($field['sanitize'] === 'int') {
                        $inputType = 'number';
                    } elseif ($field['sanitize'] === 'email') {
                        $inputType = 'email';
                    } elseif ($field['sanitize'] === 'url') {
                        $inputType = 'url';
                    }

                    echo '<input class="input" type="' . $inputType . '" id="' . $id . '" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($displayValue) . '"';
                    if ($field['maxlength']) echo ' maxlength="' . (int) $field['maxlength'] . '"';
                    if ($field['min']) echo ' min="' . (float) $field['min'] . '"';
                    if ($field['max']) echo ' max="' . (float) $field['max'] . '"';
                    if ($field['required']) echo ' required';
                    echo '>';
            }

            // Description
            if (!empty($field['description'])) {
                echo '<div class="help"><i data-lucide="info"></i> ' . htmlspecialchars($field['description']) . '</div>';
            }

            echo '</div>';
        }

        // Submit buttons
        echo '<div class="flex gap-1 mt-4" style="padding-top:1rem;border-top:1px solid var(--border);">';
        echo '<button type="submit" class="button button-primary"><i data-lucide="save"></i> Save Settings</button>';
        echo '<a href="/admin/plugins" class="button button-light"><i data-lucide="x"></i> Cancel</a>';
        echo '</div>';

        echo '</form>';
    }

    private static function getIcon(string $type): string
    {
        return match ($type) {
            'textarea' => 'align-left',
            'select'   => 'list',
            'checkbox' => 'check-square',
            default    => 'type',
        };
    }

    public static function hasForm(string $plugin): bool
    {
        return PluginSettingsRegistry::has($plugin);
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }

    private static function getFile(string $plugin): string
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $plugin)) {
            throw new \InvalidArgumentException("Invalid plugin identifier: {$plugin}");
        }
        return self::$baseDir . '/' . $plugin . '.json';
    }
}
