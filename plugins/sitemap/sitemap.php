<?php
/**
 * Sitemap Plugin – with settings
 */

use Core\Hooks;
use Core\App;
use Core\Router;
use Core\PluginSettings;
use Models\PostsModel;

// Register settings schema
PluginSettings::register('sitemap', [
    'title' => 'Sitemap Settings',
    'fields' => [
        'urls_per_sitemap' => [
            'type' => 'text',
            'label' => 'URLs per Sitemap',
            'default' => 10000,
            'maxlength' => 6,
            'description' => 'Number of URLs per sitemap file (max 50,000).',
            'sanitize' => 'int',
            'min' => 100,
            'max' => 50000,
        ],
        'include_homepage' => [
            'type' => 'checkbox',
            'label' => 'Include Homepage',
            'default' => true,
            'description' => 'Include homepage in sitemap.',
            'sanitize' => 'bool',
        ],
        'priority_homepage' => [
            'type' => 'text',
            'label' => 'Homepage Priority',
            'default' => '1.0',
            'maxlength' => 4,
            'description' => 'Priority value for homepage (0.1 – 1.0).',
            'sanitize' => 'text',
        ],
        'priority_posts' => [
            'type' => 'text',
            'label' => 'Posts Priority',
            'default' => '0.8',
            'maxlength' => 4,
            'description' => 'Priority value for posts (0.1 – 1.0).',
            'sanitize' => 'text',
        ],
    ],
]);

// Register routes
Hooks::add_action('register_routes', function (Router $router) {
    $router->addRoute('GET', '/sitemap.xml', [SitemapHandler::class, 'index']);
    $router->addRoute('GET', '/sitemap-{page}.xml', [SitemapHandler::class, 'chunk']);
});

class SitemapHandler
{
    public static function index(): void
    {
        $settings = PluginSettings::getEffective('sitemap');
        $per = max(100, min(50000, (int) ($settings['urls_per_sitemap'] ?? 10000)));

        $model = new PostsModel();
        $total = $model->publishedCount();
        $num = max(1, (int) ceil($total / $per));

        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        for ($i = 1; $i <= $num; $i++) {
            echo '  <sitemap>' . "\n";
            echo '    <loc>' . self::xmlEscape(self::baseUrl() . '/sitemap-' . $i . '.xml') . '</loc>' . "\n";
            echo '  </sitemap>' . "\n";
        }
        echo '</sitemapindex>';
    }

    public static function chunk(string $page): void
    {
        $pageNum = max(1, (int) $page);
        $settings = PluginSettings::getEffective('sitemap');
        // Cap at 1000 to match PostsModel page chunk capacity
        $per = max(50, min(1000, (int) ($settings['urls_per_sitemap'] ?? 1000)));
        $includeHome = (bool) ($settings['include_homepage'] ?? true);
        $priorityHome = $settings['priority_homepage'] ?? '1.0';
        $priorityPosts = $settings['priority_posts'] ?? '0.8';

        $model = new PostsModel();
        $total = $model->publishedCount();
        $maxChunks = max(1, (int) ceil($total / $per));

        if ($pageNum > $maxChunks) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Sitemap chunk not found';
            return;
        }

        $result = $model->publishedPaginated($pageNum, $per);

        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        if ($includeHome && $pageNum === 1) {
            echo '  <url>' . "\n";
            echo '    <loc>' . self::xmlEscape(self::baseUrl() . '/') . '</loc>' . "\n";
            echo '    <priority>' . htmlspecialchars((string) $priorityHome, ENT_QUOTES, 'UTF-8') . '</priority>' . "\n";
            echo '  </url>' . "\n";
        }

        if ($pageNum === 1) {
            try {
                $pagesModel = new \Models\PagesModel();
                foreach ($pagesModel->getAll() as $pageSlug => $pageData) {
                    $pageUrl = self::baseUrl() . '/' . rawurlencode((string) $pageSlug);
                    $pageLastmod = date('Y-m-d', (int) ($pageData['updated_at'] ?? $pageData['created_at'] ?? time()));
                    echo '  <url>' . "\n";
                    echo '    <loc>' . self::xmlEscape($pageUrl) . '</loc>' . "\n";
                    echo '    <lastmod>' . $pageLastmod . '</lastmod>' . "\n";
                    echo '    <priority>0.7</priority>' . "\n";
                    echo '  </url>' . "\n";
                }
            } catch (\Throwable) {}
        }

        foreach ($result['items'] as $post) {
            $url = self::baseUrl() . '/post/' . rawurlencode($post['slug']);
            $lastmod = date('Y-m-d', (int) ($post['updated_at'] ?? $post['created_at'] ?? time()));
            echo '  <url>' . "\n";
            echo '    <loc>' . self::xmlEscape($url) . '</loc>' . "\n";
            echo '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
            echo '    <priority>' . htmlspecialchars((string) $priorityPosts, ENT_QUOTES, 'UTF-8') . '</priority>' . "\n";
            echo '  </url>' . "\n";
        }

        echo '</urlset>';
    }

    private static function baseUrl(): string
    {
        $base = rtrim(App::$config['base_url'] ?? '', '/');
        if (empty($base)) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
            $scheme = $isHttps ? 'https' : 'http';
            $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            $base = $scheme . '://' . $host;
        }
        return $base;
    }

    private static function xmlEscape(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
