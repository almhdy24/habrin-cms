<?php

declare(strict_types=1);

namespace Core;

/**
 * Habrin CMS - Internationalization & Localization Engine
 * Supports English (en), Arabic (ar - RTL), French (fr), Spanish (es).
 */
final class I18n
{
    public const DEFAULT_LOCALE = 'en';

    /**
     * Supported locales metadata
     * @var array<string, array{name: string, native: string, dir: string, flag: string}>
     */
    public const SUPPORTED_LOCALES = [
        'en' => [
            'name'   => 'English',
            'native' => 'English',
            'dir'    => 'ltr',
            'flag'   => '🇬🇧',
        ],
        'ar' => [
            'name'   => 'Arabic',
            'native' => 'العربية',
            'dir'    => 'rtl',
            'flag'   => '🇸🇦',
        ],
        'fr' => [
            'name'   => 'French',
            'native' => 'Français',
            'dir'    => 'ltr',
            'flag'   => '🇫🇷',
        ],
        'es' => [
            'name'   => 'Spanish',
            'native' => 'Español',
            'dir'    => 'ltr',
            'flag'   => '🇪🇸',
        ],
    ];

    private static ?string $currentLocale = null;

    /**
     * Cached translation arrays keyed by locale
     * @var array<string, array<string, string>>
     */
    private static array $translations = [];

    public static function supported(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    /**
     * Initialize or detect active locale
     */
    public static function init(?string $locale = null): void
    {
        if ($locale !== null && self::isSupported($locale)) {
            self::$currentLocale = $locale;
            return;
        }
        self::detectLocale();
    }

    /**
     * Detect user's preferred locale
     */
    public static function detectLocale(): string
    {
        // 1. Explicit request query param e.g. ?lang=ar
        if (!empty($_GET['lang']) && is_string($_GET['lang'])) {
            $candidate = strtolower(trim($_GET['lang']));
            if (self::isSupported($candidate)) {
                self::setLocale($candidate);
                return $candidate;
            }
        }

        // 2. Active session
        $sessionLocale = Session::get('habrin_locale');
        if (is_string($sessionLocale) && self::isSupported($sessionLocale)) {
            self::$currentLocale = $sessionLocale;
            return $sessionLocale;
        }

        // 3. Persistent cookie
        if (!empty($_COOKIE['habrin_locale']) && is_string($_COOKIE['habrin_locale'])) {
            $candidate = strtolower(trim($_COOKIE['habrin_locale']));
            if (self::isSupported($candidate)) {
                Session::set('habrin_locale', $candidate);
                self::$currentLocale = $candidate;
                return $candidate;
            }
        }

        // 4. Site-wide default setting
        $settingsLocale = App::$config['default_language'] ?? null;
        if (is_string($settingsLocale) && self::isSupported($settingsLocale)) {
            self::$currentLocale = $settingsLocale;
            return $settingsLocale;
        }

        self::$currentLocale = self::DEFAULT_LOCALE;
        return self::DEFAULT_LOCALE;
    }

    /**
     * Check if locale is supported
     */
    public static function isSupported(string $locale): bool
    {
        return array_key_exists($locale, self::SUPPORTED_LOCALES);
    }

    /**
     * Get current active locale code
     */
    public static function getLocale(): string
    {
        if (self::$currentLocale === null) {
            self::detectLocale();
        }
        return self::$currentLocale ?? self::DEFAULT_LOCALE;
    }

    /**
     * Set active locale and persist in session and cookie
     */
    public static function setLocale(string $locale): bool
    {
        if (!self::isSupported($locale)) {
            return false;
        }

        self::$currentLocale = $locale;
        Session::set('habrin_locale', $locale);

        // Store cookie for 1 year if headers not already sent
        if (!headers_sent()) {
            setcookie('habrin_locale', $locale, [
                'expires'  => time() + 31536000,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        return true;
    }

    /**
     * Get text direction for given or current locale ('ltr' or 'rtl')
     */
    public static function getDirection(?string $locale = null): string
    {
        $loc = $locale ?? self::getLocale();
        return self::SUPPORTED_LOCALES[$loc]['dir'] ?? 'ltr';
    }

    /**
     * Is current or given locale RTL?
     */
    public static function isRtl(?string $locale = null): bool
    {
        return self::getDirection($locale) === 'rtl';
    }

    /**
     * Get array of supported locales with metadata
     * @return array<string, array{name: string, native: string, dir: string, flag: string}>
     */
    public static function getSupportedLocales(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    /**
     * Translate key into localized string
     *
     * @param string $key Translation key
     * @param array<string, mixed> $replace Replacements for :key or {key}
     * @param string|null $locale Target locale or null for current
     * @return string
     */
    public static function trans(string $key, array $replace = [], ?string $locale = null, ?string $fallback = null): string
    {
        $loc = $locale ?? self::getLocale();
        self::loadLocale($loc);

        $text = self::$translations[$loc][$key] ?? null;

        // Fallback to default English if translation is missing
        if ($text === null && $loc !== self::DEFAULT_LOCALE) {
            self::loadLocale(self::DEFAULT_LOCALE);
            $text = self::$translations[self::DEFAULT_LOCALE][$key] ?? null;
        }

        // Fallback to custom fallback or key itself if not found anywhere
        if ($text === null) {
            $text = $fallback ?? $key;
        }

        if (empty($replace)) {
            return $text;
        }

        // Interpolate :token and {token}
        foreach ($replace as $k => $val) {
            $valStr = (string) $val;
            $text = str_replace(
                [':' . $k, '{' . $k . '}'],
                $valStr,
                $text
            );
        }

        return $text;
    }

    /**
     * Load locale dictionary from languages/{locale}.php
     */
    public static function loadLocale(string $locale): void
    {
        if (isset(self::$translations[$locale])) {
            return;
        }

        $path = App::path('languages', "{$locale}.php");
        if (file_exists($path)) {
            $data = require $path;
            if (is_array($data)) {
                self::$translations[$locale] = $data;
                return;
            }
        }

        self::$translations[$locale] = [];
    }

    /**
     * Reset in-memory cached state (used for testing)
     */
    public static function reset(): void
    {
        self::$currentLocale = null;
        self::$translations = [];
    }
}
