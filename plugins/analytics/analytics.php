<?php
/**
 * Analytics Plugin – Google Analytics with settings
 */

use Core\Hooks;
use Core\View;
use Core\PluginSettings;

// Register settings schema
PluginSettings::register('analytics', [
    'title' => 'Analytics Settings',
    'fields' => [
        'tracking_id' => [
            'type' => 'text',
            'label' => 'Tracking ID',
            'default' => '',
            'maxlength' => 20,
            'description' => 'Google Analytics 4 measurement ID (G-XXXXXX) or Universal Analytics ID (UA-XXXXX-X).',
            'sanitize' => 'text',
        ],
        'enable_analytics' => [
            'type' => 'checkbox',
            'label' => 'Enable Analytics',
            'default' => true,
            'description' => 'Enable tracking code injection.',
            'sanitize' => 'bool',
        ],
        'enable_admin_tracking' => [
            'type' => 'checkbox',
            'label' => 'Track Admin Pages',
            'default' => false,
            'description' => 'Enable tracking on admin pages.',
            'sanitize' => 'bool',
        ],
    ],
]);

// Hook into body_end
Hooks::add_action('body_end', function () {
    $settings = PluginSettings::getEffective('analytics');
    $trackingId = $settings['tracking_id'] ?? '';
    $enabled = (bool) ($settings['enable_analytics'] ?? true);
    $adminTracking = (bool) ($settings['enable_admin_tracking'] ?? false);

    // Check if tracking should be injected
    if (empty($trackingId) || !$enabled) {
        return;
    }

    // Skip admin pages unless admin tracking is enabled
    if (strpos($_SERVER['REQUEST_URI'], '/admin') === 0 && !$adminTracking) {
        return;
    }

    echo <<<HTML
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$trackingId}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$trackingId}');
</script>
HTML;
});
