<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Core\App;
use Core\PluginLoader;
use Core\PluginSettings;
use Core\Hooks;

final class PluginSystemTest extends TestCase
{
    private string $originalSettings;

    protected function setUp(): void
    {
        parent::setUp();
        App::loadConfig();
        PluginSettings::init();

        $settingsFile = App::path('content', 'plugin_settings.json');
        $this->originalSettings = file_exists($settingsFile) ? (string) file_get_contents($settingsFile) : '';
    }

    protected function tearDown(): void
    {
        $settingsFile = App::path('content', 'plugin_settings.json');
        if ($this->originalSettings !== '') {
            file_put_contents($settingsFile, $this->originalSettings, LOCK_EX);
        } elseif (file_exists($settingsFile)) {
            @unlink($settingsFile);
        }
        parent::tearDown();
    }

    public function testPluginDiscoveryFindsAllCorePlugins(): void
    {
        $loader = new PluginLoader(App::path('plugins'));
        $plugins = $loader->discover();

        $this->assertArrayHasKey('markdown', $plugins);
        $this->assertArrayHasKey('seo', $plugins);
        $this->assertArrayHasKey('sitemap', $plugins);
        $this->assertArrayHasKey('analytics', $plugins);

        foreach ($plugins as $name => $info) {
            $this->assertFileExists($info['file'], "Plugin file for {$name} should exist.");
        }
    }

    public function testPluginSettingsSaveAndRetrieve(): void
    {
        // Define settings schema for testing if not already registered
        if (!PluginSettings::hasForm('test_plugin')) {
            PluginSettings::register('test_plugin', [
                'title' => 'Test Plugin',
                'fields' => [
                    'enabled' => ['type' => 'boolean', 'default' => false],
                    'api_key' => ['type' => 'text', 'required' => true],
                ],
            ]);
        }

        $saveResult = PluginSettings::save('test_plugin', [
            'enabled' => '1',
            'api_key' => 'secret_12345',
        ]);

        $this->assertTrue($saveResult['valid']);
        $this->assertEquals('secret_12345', PluginSettings::get('test_plugin', 'api_key'));
        $this->assertTrue(PluginSettings::get('test_plugin', 'enabled'));
    }

    public function testSeoPluginGeneratesMetaTags(): void
    {
        $seoFile = App::path('plugins', 'seo', 'seo.php');
        require_once $seoFile;

        // Simulate single post view
        $postData = [
            'id' => 1,
            'title' => 'Sample Article Title',
            'slug' => 'sample-article-title',
            'content' => 'This is an introductory paragraph for SEO testing.',
            'created_at' => time(),
        ];

        ob_start();
        Hooks::do_action('head', $postData);
        $headOutput = ob_get_clean();

        $this->assertStringContainsString('<meta property="og:title" content="Sample Article Title', $headOutput);
        $this->assertStringContainsString('<meta property="og:type" content="article', $headOutput);
        $this->assertStringContainsString('<meta name="description"', $headOutput);
    }

    public function testAnalyticsPluginOutputsTrackingCode(): void
    {
        $analyticsFile = App::path('plugins', 'analytics', 'analytics.php');
        require_once $analyticsFile;

        // Set tracking ID in plugin settings
        PluginSettings::save('analytics', [
            'tracking_id' => 'G-HABRIN12345',
            'anonymize_ip' => true,
        ]);

        ob_start();
        Hooks::do_action('body_end');
        $output = ob_get_clean();

        $this->assertStringContainsString('G-HABRIN12345', $output);
        $this->assertStringContainsString('gtag', $output);
    }
}
