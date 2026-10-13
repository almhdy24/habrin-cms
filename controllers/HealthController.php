<?php
/**
 * Health check endpoint
 */

declare(strict_types=1);

namespace Controllers;

use Core\App;

final class HealthController
{
    public function index(): void
    {
        $checks = [];

        // Config file exists
        $configPath = App::path('config.php');
        $checks['config_exists'] = file_exists($configPath);

        // Content & logs writable
        $checks['content_writable'] = is_dir(App::path('content')) && is_writable(App::path('content'));
        $checks['logs_writable'] = is_dir(App::path('logs')) && is_writable(App::path('logs'));

        // Plugins discovered
        $loader = new \Core\PluginLoader(App::path('plugins'));
        $checks['plugins_found'] = count($loader->discover());

        // Basic JSON response
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'ok',
            'timestamp' => time(),
            'checks' => $checks,
        ], JSON_PRETTY_PRINT);
    }
}
