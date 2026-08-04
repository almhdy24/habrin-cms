<?php

declare(strict_types=1);

namespace Core;

/**
 * ------------------------------------------------------
 * Pico JSON CMS — Session Manager (CORE)
 * ------------------------------------------------------
 *
 * CORE VERSION: 1.0.0
 *
 * Responsibilities:
 *  - Start and manage PHP sessions
 *  - Provide safe session access
 *  - Handle flash messages
 *
 * ⚠️ CORE FREEZE FILE
 * Public API MUST remain stable after v1.0.0
 * ------------------------------------------------------
 */

final class Session
{
    /**
     * Internal started flag
     */
    private static bool $started = false;

    /* =====================================================
     * Bootstrap
     * ===================================================== */

    /**
     * Start the session if not already started
     */
    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'httponly' => true,
            'secure'   => isset($_SERVER['HTTPS']),
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;
    }

    /* =====================================================
     * Basic API
     * ===================================================== */

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::start();
        return array_key_exists($key, $_SESSION);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Regenerate session ID (security)
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Destroy session completely
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    /* =====================================================
     * Flash Messages
     * ===================================================== */

    /**
     * Store a flash message
     */
    public static function flash(string $type, string $message): void
    {
        self::start();
        $_SESSION['_flash'][] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    /**
     * Retrieve and clear flash messages
     */
    public static function getFlash(): array
    {
        self::start();

        if (empty($_SESSION['_flash'])) {
            return [];
        }

        $flash = $_SESSION['_flash'];
        unset($_SESSION['_flash']);

        return $flash;
    }

    /* =====================================================
     * CSRF Protection
     * ===================================================== */

    /**
     * Generate a CSRF token and store in session
     */
    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * Validate a CSRF token
     */
    public static function validateCsrfToken(string $token): bool
    {
        self::start();
        if (empty($_SESSION['_csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }
}
