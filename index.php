<?php

declare(strict_types=1);

/**
 * Pico JSON CMS - Application Entry Point
 *
 * Author: Elmahdi
 * GitHub: https://github.com/almhdy24/pico-json-cms
 *
 * License: MIT
 */

use Core\App;

$minPhp = App::MIN_PHP_VERSION;
if (version_compare(PHP_VERSION, $minPhp, '<')) {
    http_response_code(500);
    echo "Pico JSON CMS requires PHP {$minPhp} or higher. Current version: " . PHP_VERSION;
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.php';

try {
    App::run();
} catch (Throwable $e) {
    http_response_code(500);

    if (function_exists('error_log')) {
        error_log('Pico JSON CMS fatal error: ' . $e->getMessage());
    }

    if (App::$config['debug'] ?? false) {
        echo $e;
        exit(1);
    }

    echo '<h1>Something went wrong</h1>';
    echo '<p>The application encountered an unexpected error.</p>';
    exit(1);
}
