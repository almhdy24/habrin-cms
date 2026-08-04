<?php
/**
 * SEO Plugin with Schema-Driven Settings
 */

use Core\Hooks;
use Core\View;
use Core\App;
use Core\PluginSettings;

// Register settings schema immediately
PluginSettings::register('seo', [
    'title' => 'SEO Settings',
    'fields' => [
        'default_title' => [
            'type' => 'text',
            'label' => 'Default Title',
            'default' => '',
            'maxlength' => 60,
            'description' => 'Appears in the browser tab and search results.',
            'sanitize' => 'text',
        ],
        'default_description' => [
            'type' => 'textarea',
            'label' => 'Default Description',
            'default' => '',
            'maxlength' => 160,
            'rows' => 3,
            'description' => 'Meta description for SEO (max 160 characters).',
            'sanitize' => 'text',
        ],
        'author' => [
            'type' => 'text',
            'label' => 'Author Name',
            'default' => '',
            'maxlength' => 100,
            'description' => 'Appears in the author meta tag.',
            'sanitize' => 'text',
        ],
        'enable_og_tags' => [
            'type' => 'checkbox',
            'label' => 'Enable Open Graph Tags',
            'default' => true,
            'description' => 'Enable Open Graph meta tags for social sharing.',
            'sanitize' => 'bool',
        ],
    ],
]);

// Hook into head
Hooks::add_action('head', function () {
    $settings = View::getShared('settings') ?? [];
    $pluginSettings = PluginSettings::getEffective('seo');

    $title = $pluginSettings['default_title'] ?? $settings['site_title'] ?? 'My Pico CMS';
    $desc = $pluginSettings['default_description'] ?? $settings['site_description'] ?? '';
    $author = $pluginSettings['author'] ?? '';
    $enableOg = (bool) ($pluginSettings['enable_og_tags'] ?? true);

    echo '<meta name="description" content="' . htmlspecialchars($desc) . '">' . "\n";
    if (!empty($author)) {
        echo '<meta name="author" content="' . htmlspecialchars($author) . '">' . "\n";
    }

    if ($enableOg) {
        $baseUrl = rtrim(App::$config['base_url'] ?? '', '/');
        if (empty($baseUrl)) {
            $baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        }
        $url = $baseUrl . $_SERVER['REQUEST_URI'];

        echo '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:url" content="' . htmlspecialchars($url) . '">' . "\n";

        $post = View::getShared('current_post') ?? null;
        if ($post) {
            $excerpt = strip_tags($post['content'] ?? '');
            $excerpt = mb_substr($excerpt, 0, 200);
            echo '<meta property="og:description" content="' . htmlspecialchars($excerpt) . '">' . "\n";
        } elseif (!empty($desc)) {
            echo '<meta property="og:description" content="' . htmlspecialchars($desc) . '">' . "\n";
        }
    }
});
