<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Core\Hooks;
use Core\PluginLoader;
use Models\ThemesModel;

final class SecurityTest extends TestCase
{
    public function testMarkdownStripsXssTags(): void
    {
        // Load markdown plugin if not already loaded
        $markdownPlugin = dirname(__DIR__) . '/plugins/markdown/plugin.php';
        if (file_exists($markdownPlugin)) {
            require_once $markdownPlugin;
        }

        $xssInput = 'Hello <script>alert("XSS")</script> <iframe src="javascript:alert(1)"></iframe> world';
        $rendered = Hooks::apply_filters('post_content', $xssInput, []);

        $this->assertStringNotContainsString('<script', $rendered);
        $this->assertStringNotContainsString('</script>', $rendered);
        $this->assertStringNotContainsString('<iframe', $rendered);
    }

    public function testPluginLoaderRejectsPathTraversal(): void
    {
        $tempPluginsDir = sys_get_temp_dir() . '/test_plugins_' . uniqid();
        mkdir($tempPluginsDir, 0755, true);
        $maliciousPluginDir = $tempPluginsDir . '/malicious';
        mkdir($maliciousPluginDir, 0755, true);

        // Manifest attempting path traversal to target /etc/passwd or config.php
        $manifest = [
            'name'  => 'malicious',
            'entry' => '../../config.php',
        ];
        file_put_contents($maliciousPluginDir . '/plugin.json', json_encode($manifest));

        $loader = new PluginLoader($tempPluginsDir);
        $discovered = $loader->discover();

        $this->assertArrayNotHasKey('malicious', $discovered);

        // Clean up
        @unlink($maliciousPluginDir . '/plugin.json');
        @rmdir($maliciousPluginDir);
        @rmdir($tempPluginsDir);
    }

    public function testSensitiveDirectoriesContainAccessDenialRules(): void
    {
        $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);

        $sensitiveDirs = [
            $base . '/content',
            $base . '/logs',
            $base . '/backups',
        ];

        foreach ($sensitiveDirs as $dir) {
            $htaccess = $dir . '/.htaccess';
            $this->assertFileExists($htaccess, "Missing .htaccess in {$dir}");
            $content = file_get_contents($htaccess);
            $this->assertMatchesRegularExpression('/deny from all|require all denied/i', $content);
        }

        // Uploads directory must prevent script execution
        $uploadsHtaccess = $base . '/assets/uploads/.htaccess';
        $this->assertFileExists($uploadsHtaccess);
        $uploadsContent = file_get_contents($uploadsHtaccess);
        $this->assertStringContainsString('php_flag engine off', $uploadsContent);
        $this->assertStringContainsString('Require all denied', $uploadsContent);
    }

    public function testThemeTraversalProtection(): void
    {
        $model = new ThemesModel();
        $this->assertFalse($model->isValid('../evil'));
        $this->assertFalse($model->isValid('../../etc/passwd'));
        $this->assertFalse($model->isValid('default/../../'));
        $this->assertTrue($model->isValid('default'));
    }
}
