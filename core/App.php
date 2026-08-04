<?php

declare(strict_types=1);

namespace Core;

defined('BASE_PATH') || define(
    'BASE_PATH',
    realpath(__DIR__ . '/..')
);

final class App
{
    public const CORE_VERSION = "0.9.0";
    public const MIN_PHP_VERSION = '8.1.0';

    public static array $config = [];
    public static array $plugins = [];
    public static array $pluginStatus = [];
    private static array $configCache = [];

    public static function path(string ...$segments): string
    {
        return preg_replace(
            '#/+#',
            DIRECTORY_SEPARATOR,
            BASE_PATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments)
        );
    }

    public static function run(): void
    {
        Hooks::do_action('beforeRun');
        self::sendSecurityHeaders();
        Session::start();
        Hooks::do_action('afterSessionStart');
        self::bootstrap();
        Hooks::do_action('afterBootstrap');
        self::loadConfig();
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

    private static function ensureDirectories(): void
    {
        self::ensureDir(self::path('content'));
        self::ensureDir(self::path('logs'));
    }

    private static function ensureFiles(): void
    {
        self::ensureFile('content/posts.json', '[]');
        self::ensureFile('content/settings.json', '{}');
        self::ensureFile('content/pages.json', '{}');
        self::ensureFile('content/menu.json', '[]');
        self::ensureFile('content/plugins.json', '{}');
    }

    private static function ensureDir(string $path): void
    {
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                throw new \RuntimeException("Failed to create directory: {$path}");
            }
        }
    }

    private static function ensureFile(string $relativePath, string $default): void
    {
        $path = self::path($relativePath);
        if (!file_exists($path)) {
            file_put_contents($path, $default, LOCK_EX);
        }
    }

    private static function loadConfig(): void
    {
        $configPath = self::path('config.php');
        if (!file_exists($configPath)) {
            throw new \RuntimeException("Config file missing: {$configPath}");
        }
        self::$config = require $configPath;
        if (!is_array(self::$config)) {
            throw new \RuntimeException("Invalid config format");
        }
        self::$config['base_url'] ??= '';
        self::$config['theme']    ??= 'default';
        self::$configCache = self::$config;
    }

    /**
     * Safely update a configuration value
     */
    public static function setConfig(string $key, mixed $value): bool
    {
        $configPath = self::path('config.php');
        $config = self::$config;
        $config[$key] = $value;

        // Write with proper formatting (preserve PHP syntax)
        $content = '<?php' . "\n\n";
        $content .= '/**' . "\n";
        $content .= ' * Pico JSON CMS - Main Configuration' . "\n";
        $content .= ' *' . "\n";
        $content .= ' * Author: Elmahdi' . "\n";
        $content .= ' * GitHub: https://github.com/almhdy24/pico-json-cms' . "\n";
        $content .= ' *' . "\n";
        $content .= ' * License: MIT' . "\n";
        $content .= ' */' . "\n\n";
        $content .= 'return ' . self::varExportPretty($config) . ";\n";

        if (file_put_contents($configPath, $content, LOCK_EX) === false) {
            return false;
        }

        self::$config = $config;
        return true;
    }

    /**
     * Pretty var_export with proper indentation
     */
    private static function varExportPretty(array $array, int $indent = 0): string
    {
        $indentStr = str_repeat('  ', $indent);
        $nextIndent = str_repeat('  ', $indent + 1);

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

        if (empty($parts)) {
            return "[]";
        }

        return "[\n" . implode(",\n", $parts) . "\n" . $indentStr . "]";
    }

    private static function loadPluginStatus(): void
    {
        $path = self::path('content', 'plugins.json');
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
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

        if (
            str_starts_with($uri, '/themes/') ||
            str_starts_with($uri, '/assets/')
        ) {
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
        file_put_contents(
            self::path('logs', 'app.log'),
            '[' . date('c') . "][{$level}] {$message}\n",
            FILE_APPEND | LOCK_EX
        );
    }

    private static function sendSecurityHeaders(): void
    {
        if (headers_sent()) return;
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
