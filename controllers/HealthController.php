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
        // Protect the health endpoint. If a health_key is configured, require it via header.
        $healthKey = App::$config['health_key'] ?? null;
        $allowed = false;

        if ($healthKey) {
            $provided = $_SERVER['HTTP_X_HEALTH_KEY'] ?? $_SERVER['HTTP_X_HEALTHKEY'] ?? '';
            if (is_string($provided) && hash_equals($healthKey, $provided)) {
                $allowed = true;
            }
        } else {
            // Only allow localhost by default when no key is present
            $remote = $_SERVER['REMOTE_ADDR'] ?? '';
            $allowedIps = ['127.0.0.1', '::1'];
            if (in_array($remote, $allowedIps, true)) {
                $allowed = true;
            }
        }

        if (!$allowed) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'forbidden']);
            return;
        }

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
