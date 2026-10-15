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

// Hook into body_end and admin_body_end
$analyticsRenderer = function () {
    $settings = PluginSettings::getEffective('analytics');
    $trackingId = trim((string) ($settings['tracking_id'] ?? ''));
    $enabled = (bool) ($settings['enable_analytics'] ?? true);
    $adminTracking = (bool) ($settings['enable_admin_tracking'] ?? false);

    if (empty($trackingId) || !$enabled) {
        return;
    }

    if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin') && !$adminTracking) {
        return;
    }

    if (!preg_match('/^[A-Za-z0-9-]+$/', $trackingId)) {
        return;
    }

    $safeTrackingId = htmlspecialchars($trackingId, ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$safeTrackingId}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$safeTrackingId}');
</script>
HTML;
};

Hooks::add_action('body_end', $analyticsRenderer);
Hooks::add_action('admin_body_end', $analyticsRenderer);
