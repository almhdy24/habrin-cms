<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Core\App;
use Core\View;
use Models\ThemesModel;

final class ThemeIntegrityTest extends TestCase
{
    private string $originalTheme;

    protected function setUp(): void
    {
        parent::setUp();
        App::loadConfig();
        $this->originalTheme = App::$config['theme'] ?? 'default';
        View::clearCache();

        // Ensure plugins like markdown are loaded so template filters work
        $md = App::path('plugins', 'markdown', 'plugin.php');
        if (file_exists($md)) {
            require_once $md;
        }
    }

    protected function tearDown(): void
    {
        App::setConfig('theme', $this->originalTheme);
        View::clearCache();
        parent::tearDown();
    }

    public function testAllThemesAreDiscovered(): void
    {
        $themesModel = new ThemesModel();
        $themes = $themesModel->getAll();

        $this->assertArrayHasKey('default', $themes);
        $this->assertArrayHasKey('cleanblog', $themes);
        $this->assertArrayHasKey('dark', $themes);
        $this->assertArrayHasKey('magazine', $themes);
    }

    public function testThemeTemplatesExistForEveryTheme(): void
    {
        $themeNames = ['default', 'cleanblog', 'dark', 'magazine'];
        $requiredPages = ['pages/home.php', 'pages/single.php', 'pages/page.php', 'pages/404.php', 'layouts/site.php'];

        foreach ($themeNames as $name) {
            foreach ($requiredPages as $page) {
                $file = App::path('themes', $name, $page);
                $this->assertFileExists($file, "Theme '{$name}' must have template '{$page}'");
            }
        }
    }

    public function testThemeSwitchingAndRendering(): void
    {
        $themesModel = new ThemesModel();
        $samplePost = [
            'id' => 999,
            'title' => 'Test Theme Switching Article',
            'slug' => 'test-theme-switching-article',
            'content' => "## Subheading\n\nTesting **bold** markdown rendering in active theme.",
            'created_at' => time(),
            'status' => 'published',
        ];

        $themesToTest = ['default', 'cleanblog', 'dark', 'magazine'];

        foreach ($themesToTest as $theme) {
            $switched = $themesModel->activate($theme);
            $this->assertTrue($switched, "Should successfully activate theme '{$theme}'");
            $this->assertEquals($theme, $themesModel->getActive());

            // Render Home view in active theme
            ob_start();
            View::render('pages/home', [
                'posts' => [999 => $samplePost],
                'currentPage' => 1,
                'totalPages' => 1,
                'settings' => ['site_title' => 'Habrin CMS Test', 'site_description' => 'Test Description'],
                'menuItems' => [],
            ]);
            $htmlHome = ob_get_clean();

            $this->assertNotEmpty($htmlHome, "Home page HTML should not be empty for theme '{$theme}'");
            $this->assertStringContainsString('Test Theme Switching Article', $htmlHome, "Home page should display post title in theme '{$theme}'");

            // Render Single view in active theme
            ob_start();
            View::render('pages/single', [
                'post' => $samplePost,
                'settings' => ['site_title' => 'Habrin CMS Test'],
                'menuItems' => [],
            ]);
            $htmlSingle = ob_get_clean();

            $this->assertNotEmpty($htmlSingle, "Single post HTML should not be empty for theme '{$theme}'");
            $this->assertStringContainsString('Test Theme Switching Article', $htmlSingle);
            $this->assertStringContainsString('Subheading</h2>', $htmlSingle);

            // Render Static Page view in active theme
            ob_start();
            View::render('pages/page', [
                'page' => [
                    'title' => 'About Us',
                    'content' => 'Welcome to the about page.',
                ],
                'settings' => ['site_title' => 'Habrin CMS Test'],
                'menuItems' => [],
            ]);
            $htmlPage = ob_get_clean();

            $this->assertNotEmpty($htmlPage, "Static page HTML should not be empty for theme '{$theme}'");
            $this->assertStringContainsString('About Us', $htmlPage);
        }
    }
}
