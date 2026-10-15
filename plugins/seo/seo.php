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
Hooks::add_action('head', function (mixed $maybePost = null) {
    $settings = View::getShared('settings') ?? [];
    $pluginSettings = PluginSettings::getEffective('seo');

    $post = is_array($maybePost) ? $maybePost : (View::getShared('current_post') ?? null);
    $siteTitle = $settings['site_title'] ?? 'Habrin CMS';
    $defaultTitle = !empty($pluginSettings['default_title']) ? $pluginSettings['default_title'] : $siteTitle;
    $title = $post ? ($post['title'] ?? $defaultTitle) : $defaultTitle;

    $siteDesc = $settings['site_description'] ?? '';
    $defaultDesc = !empty($pluginSettings['default_description']) ? $pluginSettings['default_description'] : $siteDesc;
    if ($post) {
        $plain = preg_replace('/\s+/', ' ', trim(strip_tags((string) ($post['content'] ?? ''))));
        $desc = mb_substr($plain, 0, 160);
        if ($desc === '') $desc = $defaultDesc;
    } else {
        $desc = $defaultDesc;
    }

    $author = $pluginSettings['author'] ?? '';
    $enableOg = (bool) ($pluginSettings['enable_og_tags'] ?? true);

    if (!empty($desc)) {
        echo '<meta name="description" content="' . htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }
    if (!empty($settings['site_keywords'])) {
        echo '<meta name="keywords" content="' . htmlspecialchars($settings['site_keywords'], ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }
    if (!empty($author)) {
        echo '<meta name="author" content="' . htmlspecialchars($author, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    $baseUrl = rtrim(App::$config['base_url'] ?? '', '/');
    if (empty($baseUrl)) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
        $scheme = $isHttps ? 'https' : 'http';
        $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $baseUrl = $scheme . '://' . $host;
    }
    $reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $canonicalUrl = $baseUrl . $reqPath;

    echo '<link rel="canonical" href="' . htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') . '">' . "\n";

    if ($enableOg) {
        $ogType = $post ? 'article' : 'website';
        echo '<meta property="og:title" content="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        echo '<meta property="og:type" content="' . $ogType . '">' . "\n";
        echo '<meta property="og:url" content="' . htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        if (!empty($desc)) {
            echo '<meta property="og:description" content="' . htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        }
    }
});
