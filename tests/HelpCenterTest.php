<?php

declare(strict_types=1);

namespace Tests;

use Controllers\AdminHelpController;
use Core\Session;
use PHPUnit\Framework\TestCase;

class HelpCenterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::start();
        $_SESSION['is_admin'] = true;
        $_SESSION['user'] = 'admin';
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    public function testHelpGuidesCollection(): void
    {
        $controller = new AdminHelpController();
        $guides = $controller->getGuides();

        $this->assertIsArray($guides);
        $this->assertCount(8, $guides);

        $expectedKeys = [
            'getting-started',
            'publishing',
            'markdown-media',
            'themes-guide',
            'plugins-guide',
            'backup-recovery',
            'security-guide',
            'multilingual-guide'
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $guides, "Expected guide {$key} to exist in Help Center.");
            $guide = $guides[$key];
            $this->assertNotEmpty($guide['id']);
            $this->assertNotEmpty($guide['icon']);
            $this->assertNotEmpty($guide['title']);
            $this->assertNotEmpty($guide['category']);
            $this->assertNotEmpty($guide['summary']);
            $this->assertNotEmpty($guide['content']);
        }
    }

    public function testMultilingualGuideCoversRtl(): void
    {
        $controller = new AdminHelpController();
        $guides = $controller->getGuides();

        $this->assertArrayHasKey('multilingual-guide', $guides);
        $content = $guides['multilingual-guide']['content'];
        $this->assertStringContainsString('RTL', $content);
        $this->assertStringContainsString('Arabic', $content);
        $this->assertStringContainsString('languages/', $content);
    }
}
