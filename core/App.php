<?php

declare(strict_types=1);

namespace Core;

final class App
{
    public const CORE_VERSION = '0.9.0';
    public const MIN_PHP_VERSION = '8.1.0';

    public static array $config = [];
    public static array $plugins = [];
    public static array $pluginStatus = [];
    private static array $configCache = [];

    public static function path(string ...$segments): string
    {
        $base = rtrim(BASE_PATH, DIRECTORY_SEPARATOR);
        $parts = array_filter($segments, static fn ($part) => $part !== '');

        if ($parts === []) {
            return $base;
        }

        return preg_replace(
            '#/+#',
            DIRECTORY_SEPARATOR,
            $base . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts)
        ) ?? $base;
    }

    public static function run(): void
    {
        self::bootstrap();
        self::loadConfig();
        self::applyRuntimeSettings();

        Hooks::do_action('beforeRun');
        self::sendSecurityHeaders();
        Session::start();
        Hooks::do_action('afterSessionStart');
        Hooks::do_action('afterBootstrap');
        Hooks::do_action('afterConfigLoaded');
        self::loadPluginStatus();
        self::loadPlugins();
        Hooks::do_action('afterPluginsLoaded');
        self::guardInstaller();
        self::loadSettings();
        self::loadMenu();
        Hooks::do_action('beforeDispatch');
        self::dispatch();
        Hooks::do_action('afterDispatch');
    }

    private static function bootstrap(): void
    {
        self::ensureDirectories();
        self::ensureFiles();
        \Core\PluginSettings::init();
    }

    private static function applyRuntimeSettings(): void
    {
        $debug = (bool) (self::$config['debug'] ?? false);
        error_reporting($debug ? E_ALL : E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');
    }

    private static function ensureDirectories(): void
    {
        foreach (['content', 'logs'] as $dir) {
            self::ensureDir(self::path($dir));
        }
    }

    private static function ensureFiles(): void
    {
        foreach ([
            'content/posts.json' => '[]',
            'content/settings.json' => '{}',
            'content/pages.json' => '{}',
            'content/menu.json' => '[]',
            'content/plugins.json' => '{}',
        ] as $relativePath => $default) {
            self::ensureFile($relativePath, $default);
        }
    }

    private static function ensureDir(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0755, true) && !is_dir($path)) {
            throw new \RuntimeException("Failed to create directory: {$path}");
        }
    }

    private static function ensureFile(string $relativePath, string $default): void
    {
        $path = self::path($relativePath);
        $dir = dirname($path);

        if (!is_dir($dir)) {
            self::ensureDir($dir);
        }

        if (!file_exists($path)) {
            if (file_put_contents($path, $default, LOCK_EX) === false) {
                throw new \RuntimeException("Failed to create file: {$path}");
            }
        }
    }

    public static function loadConfig(): void
    {
        $configPath = self::path('config.php');
        if (!file_exists($configPath)) {
            throw new \RuntimeException("Config file missing: {$configPath}");
        }

        $defaults = [
            'app_name' => 'Pico JSON CMS',
            'base_url' => '',
            'theme' => 'default',
            'debug' => false,
            'admin_user' => 'admin',
            'admin_password' => 'secret123',
        ];

        $config = require $configPath;
        if (!is_array($config)) {
            throw new \RuntimeException('Invalid config format');
        }

        self::$config = array_replace($defaults, $config);
        self::$config['base_url'] ??= '';
        self::$config['theme'] ??= 'default';
        self::$configCache = self::$config;
    }

    public static function setConfig(string $key, mixed $value): bool
    {
        $path = self::path('config.php');
        $config = self::$config;
        $config[$key] = $value;

        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * Pico JSON CMS - Main Configuration\n";
        $content .= " *\n";
        $content .= " * Author: Elmahdi\n";
        $content .= " * GitHub: https://github.com/almhdy24/pico-json-cms\n";
        $content .= " *\n";
        $content .= " * License: MIT\n";
        $content .= " */\n\n";
        $content .= 'return ' . self::varExportPretty($config) . ";\n";

        if (file_put_contents($path, $content, LOCK_EX) === false) {
            return false;
        }

        self::$config = $config;
        return true;
    }

    private static function varExportPretty(array $array, int $indent = 0): string
    {
        $indentStr = str_repeat('  ', $indent);
        $parts = [];

        foreach ($array as $key => $value) {
            $keyStr = var_export($key, true);
            if (is_array($value)) {
                $valueStr = self::varExportPretty($value, $indent + 1);
                $parts[] = $indentStr . '  ' . $keyStr . ' => ' . $valueStr;
            } else {
                $valueStr = var_export($value, true);
                $parts[] = $indentStr . '  ' . $keyStr . ' => ' . $valueStr;
            }
        }

        if ($parts === []) {
            return '[]';
        }

        return "[\n" . implode(",\n", $parts) . "\n" . $indentStr . ']';
    }

    private static function loadPluginStatus(): void
    {
        $path = self::path('content', 'plugins.json');
        if (file_exists($path)) {
            $data = json_decode((string) file_get_contents($path), true);
            if (is_array($data)) {
                self::$pluginStatus = $data;
                return;
            }
        }

        self::$pluginStatus = [];
    }

    private static function loadPlugins(): void
    {
        $loader = new PluginLoader(self::path('plugins'));
        $allPlugins = $loader->discover();

        foreach ($allPlugins as $pluginName => $info) {
            if (!isset($info['file']) || !is_string($info['file']) || !file_exists($info['file'])) {
                self::log("Plugin '{$pluginName}' entry missing", 'error');
                continue;
            }

            $enabled = !isset(self::$pluginStatus[$pluginName]) || self::$pluginStatus[$pluginName] !== false;
            if ($enabled) {
                require_once $info['file'];
                self::$plugins[$pluginName] = $info['manifest'] ?? ['name' => $pluginName];
                self::log("Plugin '{$pluginName}' loaded", 'info');
            } else {
                self::log("Plugin '{$pluginName}' disabled", 'info');
            }
        }
    }

    private static function loadSettings(): void
    {
        try {
            $settingsModel = new \Models\SettingsModel();
            $settings = $settingsModel->all();
            View::share(['settings' => $settings]);
        } catch (\Throwable $e) {
            View::share(['settings' => []]);
            self::log('Failed to load settings: ' . $e->getMessage(), 'warning');
        }
    }

    private static function loadMenu(): void
    {
        try {
            $menuModel = new \Models\MenuModel();
            $menuItems = $menuModel->getItems();
            View::share(['menuItems' => $menuItems]);
        } catch (\Throwable $e) {
            View::share(['menuItems' => []]);
            self::log('Failed to load menu: ' . $e->getMessage(), 'warning');
        }
    }

    private static function dispatch(): void
    {
        $router = new Router();
        $router->dispatch();
    }

    private static function guardInstaller(): void
    {
        $installed = file_exists(self::path('content', '.installed'));
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        if (str_starts_with($uri, '/themes/') || str_starts_with($uri, '/assets/')) {
            return;
        }

        if (!$installed && !str_starts_with($uri, '/installer')) {
            header('Location: /installer');
            exit;
        }

        if ($installed && str_starts_with($uri, '/installer')) {
            header('Location: /');
            exit;
        }
    }

    public static function log(string $message, string $level = 'info'): void
    {
        $logDir = self::path('logs');
        if (!is_dir($logDir)) {
            self::ensureDir($logDir);
        }

        $entry = '[' . date('c') . "][{$level}] {$message}\n";
        file_put_contents(self::path('logs', 'app.log'), $entry, FILE_APPEND | LOCK_EX);
    }

    private static function sendSecurityHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
}
