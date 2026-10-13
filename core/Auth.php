<?php

declare(strict_types=1);

namespace Core;

/**
 * ------------------------------------------------------
 * Pico JSON CMS — Authentication (CORE)
 * ------------------------------------------------------
 *
 * CORE VERSION: 1.0.0
 *
 * Responsibilities:
 *  - Admin authentication state
 *  - Login / logout handling
 *  - Access guarding
 *
 * ⚠️ CORE FREEZE FILE
 * Authentication mechanism MAY evolve internally,
 * but public API MUST remain stable.
 * ------------------------------------------------------
 */

final class Auth
{
    private const SESSION_TIMEOUT = 1800; // 30 minutes

    /**
     * Check if current user is admin
     */
    public static function check(): bool
    {
        $isAdmin = Session::get('is_admin') === true;
        if (!$isAdmin) {
            return false;
        }

        $last = (int) (Session::get('last_activity', 0));
        if ($last > 0 && (time() - $last) > self::SESSION_TIMEOUT) {
            // session expired
            self::logout();
            return false;
        }

        // refresh last activity
        Session::set('last_activity', time());
        return true;
    }

    /**
     * Require admin access or redirect
     */
    public static function requireAdmin(): void
    {
        if (!self::check()) {
            header('Location: /admin/login');
            exit;
        }
    }

    /**
     * Mark current session as admin
     */
    public static function login(): void
    {
        Session::regenerate();
        Session::set('is_admin', true);
        Session::set('last_activity', time());
    }

    /**
     * Remove admin privileges
     */
    public static function logout(): void
    {
        Session::remove('is_admin');
        Session::remove('last_activity');
    }
}
