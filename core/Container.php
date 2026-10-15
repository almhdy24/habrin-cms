<?php

declare(strict_types=1);

namespace Core;

/**
 * ------------------------------------------------------
 * Habrin CMS — Service Container (CORE)
 * ------------------------------------------------------
 *
 * Minimal dependency container.
 * No autowiring. No reflection. No magic.
 *
 * CORE FREEZE FILE
 * ------------------------------------------------------
 */

final class Container
{
    /**
     * Registered services
     */
    private static array $services = [];

    /**
     * Register a service
     */
    public static function set(string $id, callable $factory): void
    {
        self::$services[$id] = $factory;
    }

    /**
     * Resolve a service
     */
    public static function get(string $id): mixed
    {
        if (!isset(self::$services[$id])) {
            throw new \RuntimeException("Service not found: {$id}");
        }

        return self::$services[$id]();
    }

    /**
     * Check if a service exists
     */
    public static function has(string $id): bool
    {
        return isset(self::$services[$id]);
    }
}