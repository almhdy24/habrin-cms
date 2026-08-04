<?php

declare(strict_types=1);

namespace Core;

final class Hooks
{
    private static array $actions = [];
    private static array $filters = [];
    private static bool $logErrors = true;
    private static bool $throwOnError = false;
    private static int $sequence = 0;

    public static function setThrowOnError(bool $enabled): void
    {
        self::$throwOnError = $enabled;
    }

    public static function setErrorLogging(bool $enabled): void
    {
        self::$logErrors = $enabled;
    }

    // ---------- Actions ----------

    public static function add_action(string $hook, callable $callback, int $priority = 10): void
    {
        self::$actions[$hook][] = [
            'priority' => $priority,
            'sequence' => ++self::$sequence,
            'callback' => $callback,
        ];
        usort(self::$actions[$hook], function ($a, $b) {
            return ($a['priority'] <=> $b['priority']) ?: ($a['sequence'] <=> $b['sequence']);
        });
    }

    public static function do_action(string $hook, ...$args): void
    {
        if (empty(self::$actions[$hook])) return;
        $callbacks = self::$actions[$hook];
        foreach ($callbacks as $item) {
            try {
                $item['callback'](...$args);
            } catch (\Throwable $e) {
                if (self::$logErrors) {
                    App::log("Hook '{$hook}' error: " . $e->getMessage(), 'error');
                }
                if (self::$throwOnError) {
                    throw $e;
                }
            }
        }
    }

    public static function remove_action(string $hook, callable $callback, int $priority = 10): bool
    {
        return self::removeHook(self::$actions, $hook, $callback, $priority);
    }

    public static function has_action(string $hook): bool
    {
        return !empty(self::$actions[$hook]);
    }

    public static function get_action(string $hook): array
    {
        return self::$actions[$hook] ?? [];
    }

    // ---------- Filters ----------

    public static function add_filter(string $hook, callable $callback, int $priority = 10): void
    {
        self::$filters[$hook][] = [
            'priority' => $priority,
            'sequence' => ++self::$sequence,
            'callback' => $callback,
        ];
        usort(self::$filters[$hook], function ($a, $b) {
            return ($a['priority'] <=> $b['priority']) ?: ($a['sequence'] <=> $b['sequence']);
        });
    }

    public static function apply_filters(string $hook, mixed $value, ...$args): mixed
    {
        if (empty(self::$filters[$hook])) return $value;
        $callbacks = self::$filters[$hook];
        foreach ($callbacks as $item) {
            try {
                $value = $item['callback']($value, ...$args);
            } catch (\Throwable $e) {
                if (self::$logErrors) {
                    App::log("Filter '{$hook}' error: " . $e->getMessage(), 'error');
                }
                if (self::$throwOnError) {
                    throw $e;
                }
                // continue with current value
            }
        }
        return $value;
    }

    public static function remove_filter(string $hook, callable $callback, int $priority = 10): bool
    {
        return self::removeHook(self::$filters, $hook, $callback, $priority);
    }

    public static function has_filter(string $hook): bool
    {
        return !empty(self::$filters[$hook]);
    }

    public static function get_filter(string $hook): array
    {
        return self::$filters[$hook] ?? [];
    }

    private static function removeHook(array &$storage, string $hook, callable $callback, int $priority): bool
    {
        if (empty($storage[$hook])) return false;
        foreach ($storage[$hook] as $i => $item) {
            if ($item['priority'] === $priority && $item['callback'] === $callback) {
                unset($storage[$hook][$i]);
                $storage[$hook] = array_values($storage[$hook]);
                return true;
            }
        }
        return false;
    }

    public static function get_actions(): array { return self::$actions; }
    public static function get_filters(): array { return self::$filters; }
    public static function clear(): void { self::$actions = []; self::$filters = []; }
}
