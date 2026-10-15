<?php

declare(strict_types=1);

namespace Core;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

final class View
{
    private static ?Engine $engine = null;
    private static array $shared = [];

    private static function engine(): Engine
    {
        if (self::$engine !== null) {
            return self::$engine;
        }

        $config = App::$config;
        $theme = $config['theme'] ?? 'default';

        // Sanitize theme name
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $theme)) {
            $theme = 'default';
        }

        $themePath = App::path('themes', $theme);
        $defaultPath = App::path('themes', 'default');

        // Fallback if active theme doesn't exist
        if (!is_dir($themePath)) {
            $theme = 'default';
            $themePath = $defaultPath;
        }

        // Create engine with active theme as root
        self::$engine = new Engine($themePath);

        // Set custom resolver with fallback
        self::$engine->setResolveTemplatePath(
            new ThemeTemplateResolver($themePath, $defaultPath)
        );

        // Shared data
        self::$engine->addData(self::$shared);
        self::$engine->addData([
            'config' => $config,
            'flash' => Session::getFlash(),
            'csp_nonce' => $config['csp_nonce'] ?? null,
        ]);

        // Habrin view extensions
        self::$engine->loadExtension(new class implements ExtensionInterface {
            public function register(Engine $engine): void
            {
                $engine->registerFunction('hook', function (string $name, ...$args) {
                    Hooks::do_action($name, ...$args);
                });
                $engine->registerFunction('filter', function (string $name, mixed $value, ...$args) {
                    return apply_filters($name, $value, ...$args);
                });
                $engine->registerFunction('asset', function (string $path) {
                    return rtrim(App::$config['base_url'] ?? '', '/') . '/' . ltrim($path, '/');
                });
                $engine->registerFunction('route', function (string $path) {
                    return rtrim(App::$config['base_url'] ?? '', '/') . '/' . ltrim($path, '/');
                });
                $engine->registerFunction('csrf', function () {
                    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(Session::csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
                });
                $engine->registerFunction('is_admin', function () {
                    return \Core\Auth::check();
                });
                // Return CSP nonce value for template use
                $engine->registerFunction('nonce', function () {
                    return App::$config['csp_nonce'] ?? '';
                });
                // Return nonce attribute for script/style tags
                $engine->registerFunction('nonce_attr', function () {
                    $n = App::$config['csp_nonce'] ?? '';
                    return $n ? ' nonce="' . htmlspecialchars($n, ENT_QUOTES, 'UTF-8') . '"' : '';
                });
            }
        });

        return self::$engine;
    }

    public static function share(array $data): void
    {
        self::$shared = array_merge(self::$shared, $data);
        if (self::$engine !== null) {
            self::$engine->addData($data);
        }
    }

    public static function getShared(?string $key = null): mixed
    {
        if ($key === null) {
            return self::$shared;
        }
        return self::$shared[$key] ?? null;
    }

    public static function render(string $view, array $data = []): void
    {
        Hooks::do_action('before_render', $view, $data);
        try {
            echo self::engine()->render($view, $data);
        } finally {
            Hooks::do_action('after_render', $view, $data);
        }
    }

    public static function clearCache(): void
    {
        self::$engine = null;
    }
}
