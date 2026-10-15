<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Core\App;
use Core\Hooks;

final class MarkdownTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        App::loadConfig();

        // Load markdown plugin
        $markdownPlugin = App::path('plugins', 'markdown', 'plugin.php');
        if (file_exists($markdownPlugin)) {
            require_once $markdownPlugin;
        }
    }

    public function testBasicMarkdownRendering(): void
    {
        $input = "# Hello World\n\nThis is **bold** and *italic* text.";
        $output = apply_filters('post_content', $input, []);

        $this->assertStringContainsString('<h1>Hello World</h1>', $output);
        $this->assertStringContainsString('<strong>bold</strong>', $output);
        $this->assertStringContainsString('<em>italic</em>', $output);
    }

    public function testMarkdownImageRendering(): void
    {
        $input = "Here is an image:\n\n![A sample picture](/assets/uploads/sample.png)";
        $output = apply_filters('post_content', $input, []);

        $this->assertStringContainsString('<img', $output);
        $this->assertStringContainsString('src="/assets/uploads/sample.png"', $output);
        $this->assertStringContainsString('alt="A sample picture"', $output);
    }

    public function testMarkdownTablesAndStrikethrough(): void
    {
        $input = "This is ~~deleted~~ text.\n\n| Col 1 | Col 2 |\n| --- | --- |\n| Val 1 | Val 2 |";
        $output = apply_filters('post_content', $input, []);

        $this->assertStringContainsString('<del>deleted</del>', $output);
        $this->assertStringContainsString('<table>', $output);
        $this->assertStringContainsString('<td>Val 1</td>', $output);
    }

    public function testFrontMatterIsParsedAndStripped(): void
    {
        $input = "---\ntitle: Custom Front Matter Title\nauthor: Jane Doe\n---\n\n# Actual Content\n\nParagraph text here.";
        $post = [];
        $output = apply_filters('post_content', $input, $post);

        // Front matter headers should NOT appear in rendered output
        $this->assertStringNotContainsString('title: Custom Front Matter Title', $output);
        $this->assertStringNotContainsString('author: Jane Doe', $output);
        $this->assertStringContainsString('<h1>Actual Content</h1>', $output);
        $this->assertStringContainsString('<p>Paragraph text here.</p>', $output);
    }

    public function testXssSanitizationInMarkdown(): void
    {
        $input = "Dangerous content: <script>alert('pwned')</script>\n\n<img src=x onerror=alert(1)>";
        $output = apply_filters('post_content', $input, []);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringNotContainsString('onerror=alert(1)', $output);
    }
}
