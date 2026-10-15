<?php

declare(strict_types=1);

namespace Tests;

use Core\I18n;
use PHPUnit\Framework\TestCase;

class I18nTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset I18n state
        I18n::init('en');
    }

    public function testSupportedLanguages(): void
    {
        $supported = I18n::supported();
        $this->assertArrayHasKey('en', $supported);
        $this->assertArrayHasKey('ar', $supported);
        $this->assertArrayHasKey('fr', $supported);
        $this->assertArrayHasKey('es', $supported);
    }

    public function testDefaultLanguageIsEnglish(): void
    {
        $this->assertSame('en', I18n::getLocale());
        $this->assertSame('ltr', I18n::getDirection());
        $this->assertFalse(I18n::isRtl());
    }

    public function testSwitchingToArabicEnablesRtl(): void
    {
        I18n::setLocale('ar');
        $this->assertSame('ar', I18n::getLocale());
        $this->assertSame('rtl', I18n::getDirection());
        $this->assertTrue(I18n::isRtl());
        $this->assertSame('ltr', I18n::getDirection('en'));
        $this->assertSame('rtl', I18n::getDirection('ar'));
    }

    public function testFallbackWhenUnsupportedLocaleRequested(): void
    {
        I18n::setLocale('en');
        $result = I18n::setLocale('de'); // Not in supported list
        $this->assertFalse($result);
        $this->assertSame('en', I18n::getLocale());
        $this->assertSame('ltr', I18n::getDirection());
    }

    public function testTranslationKeyLookup(): void
    {
        I18n::setLocale('en');
        $this->assertSame('Dashboard', __('nav.dashboard'));
        $this->assertSame('Articles', __('nav.posts'));

        I18n::setLocale('ar');
        $this->assertSame('لوحة التحكم', __('nav.dashboard'));
        $this->assertSame('المقالات', __('nav.posts'));

        I18n::setLocale('fr');
        $this->assertSame('Tableau de bord', __('nav.dashboard'));
        $this->assertSame('Articles', __('nav.posts'));

        I18n::setLocale('es');
        $this->assertSame('Panel de control', __('nav.dashboard'));
        $this->assertSame('Artículos', __('nav.posts'));
    }

    public function testParameterInterpolation(): void
    {
        I18n::setLocale('en');
        $translated = __('posts.bulk_success', ['count' => '5']);
        $this->assertSame('Bulk operation applied to 5 article(s).', $translated);

        I18n::setLocale('ar');
        $translatedAr = __('posts.bulk_success', ['count' => '3']);
        $this->assertSame('تم تطبيق الإجراء على 3 مقال(ات).', $translatedAr);
    }

    public function testMissingKeyReturnsKeyOrFallback(): void
    {
        I18n::setLocale('en');
        $this->assertSame('nonexistent.key.test', __('nonexistent.key.test'));
        $this->assertSame('Custom Fallback', __('nonexistent.key.test', [], 'Custom Fallback'));
    }

    public function testGlobalHelperFunctions(): void
    {
        I18n::setLocale('en');
        $this->assertSame('en', current_lang());
        $this->assertSame('ltr', current_dir());
        $this->assertFalse(is_rtl());

        I18n::setLocale('ar');
        $this->assertSame('ar', current_lang());
        $this->assertSame('rtl', current_dir());
        $this->assertTrue(is_rtl());
    }

    public function testTranslationDictionariesKeyConsistency(): void
    {
        $en = require BASE_PATH . '/languages/en.php';
        $ar = require BASE_PATH . '/languages/ar.php';
        $fr = require BASE_PATH . '/languages/fr.php';
        $es = require BASE_PATH . '/languages/es.php';

        $this->assertIsArray($en);
        $this->assertIsArray($ar);
        $this->assertIsArray($fr);
        $this->assertIsArray($es);

        $coreKeys = [
            'nav.dashboard',
            'nav.posts',
            'nav.pages',
            'nav.media',
            'nav.themes',
            'nav.plugins',
            'nav.settings',
            'nav.system',
            'nav.help',
            'posts.bulk_actions',
            'posts.bulk_publish',
            'posts.bulk_draft',
            'posts.bulk_trash',
            'posts.bulk_success',
            'system.title',
            'help.title'
        ];

        foreach ($coreKeys as $key) {
            $this->assertArrayHasKey($key, $en, "Missing key {$key} in en.php");
            $this->assertArrayHasKey($key, $ar, "Missing key {$key} in ar.php");
            $this->assertArrayHasKey($key, $fr, "Missing key {$key} in fr.php");
            $this->assertArrayHasKey($key, $es, "Missing key {$key} in es.php");
        }
    }
}
