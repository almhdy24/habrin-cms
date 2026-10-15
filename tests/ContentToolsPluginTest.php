<?php

declare(strict_types=1);

namespace Tests;

use Core\PluginSettings;
use PHPUnit\Framework\TestCase;

require_once BASE_PATH . '/plugins/content_tools/content_tools.php';

class ContentToolsPluginTest extends TestCase
{
    public function testPluginSettingsRegistered(): void
    {
        $schema = \Core\PluginSettingsRegistry::get('content_tools');
        $this->assertNotNull($schema);
        $this->assertSame('Content Tools Settings', $schema['title']);
        $this->assertArrayHasKey('reading_speed', $schema['fields']);
        $this->assertArrayHasKey('min_words', $schema['fields']);
        $this->assertArrayHasKey('require_excerpt', $schema['fields']);
        $this->assertArrayHasKey('require_tags', $schema['fields']);
        $this->assertArrayHasKey('check_images_alt', $schema['fields']);
        $this->assertArrayHasKey('show_public_badge', $schema['fields']);
    }

    public function testWordCountCalculation(): void
    {
        $content = "# Hello World\n\nThis is a **clean** test of *content* tools with `code` and [links](http://example.com).";
        $words = content_tools_word_count($content);
        $this->assertGreaterThanOrEqual(10, $words);

        $empty = "";
        $this->assertSame(0, content_tools_word_count($empty));
    }

    public function testReadingTimeCalculation(): void
    {
        // 200 words at 200 wpm = 1 minute
        $words200 = implode(' ', array_fill(0, 200, 'word'));
        $this->assertSame(1, content_tools_reading_time($words200, 200));

        // 450 words at 200 wpm = ceil(450 / 200) = 3 minutes
        $words450 = implode(' ', array_fill(0, 450, 'word'));
        $this->assertSame(3, content_tools_reading_time($words450, 200));

        // Very short text defaults to minimum 1 minute
        $words10 = "Just a quick ten words note for testing purposes here now.";
        $this->assertSame(1, content_tools_reading_time($words10, 200));
    }

    public function testAnalyzerFlagsShortTitle(): void
    {
        $analysis = content_tools_analyze('Hi', 'A long enough body paragraph to satisfy words threshold but short title.', 'Excerpt provided', ['tag1']);
        $this->assertArrayHasKey('title', $analysis['checks']);
        $this->assertSame('warn', $analysis['checks']['title']['status']);
        $this->assertFalse($analysis['passes']);
    }

    public function testAnalyzerFlagsMissingExcerpt(): void
    {
        $body = implode(' ', array_fill(0, 250, 'content'));
        $analysis = content_tools_analyze('Optimal Headline Here', $body, '', ['tech']);
        $this->assertArrayHasKey('excerpt', $analysis['checks']);
        $this->assertSame('warn', $analysis['checks']['excerpt']['status']);
    }

    public function testAnalyzerFlagsMissingImageAlt(): void
    {
        $body = "Check this out: ![](media/chart.png) and some more content words.";
        $analysis = content_tools_analyze('Optimal Headline Here', $body, 'Summary here', ['tech']);
        $this->assertArrayHasKey('images', $analysis['checks']);
        $this->assertSame('warn', $analysis['checks']['images']['status']);

        // With descriptive alt text
        $bodyWithAlt = "Check this out: ![Sales Quarterly Chart](media/chart.png) and some more words.";
        $analysisWithAlt = content_tools_analyze('Optimal Headline Here', $bodyWithAlt, 'Summary here', ['tech']);
        $this->assertSame('ok', $analysisWithAlt['checks']['images']['status']);
    }

    public function testAnalyzerPassesCompleteArticle(): void
    {
        $body = implode(' ', array_fill(0, 250, 'insightful'));
        $analysis = content_tools_analyze('Complete and Thorough Article', $body, 'A good summary', ['tech', 'news']);
        $this->assertTrue($analysis['passes']);
        $this->assertSame('ok', $analysis['checks']['title']['status']);
        $this->assertSame('ok', $analysis['checks']['length']['status']);
        $this->assertSame('ok', $analysis['checks']['excerpt']['status']);
    }
}
