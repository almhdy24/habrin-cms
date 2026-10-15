<?php

/**
 * Habrin CMS — Global Functions
 */

use Core\Hooks;

function app_config(string $key, mixed $default = null): mixed
{
    return Core\App::$config[$key] ?? $default;
}

function app_env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

// ---------- Input validation / sanitization helpers ----------
function sanitize(string $value): string
{
    // Trim, normalize newlines, and remove control characters
    $value = trim(preg_replace('/\r\n|\r|\n/', "\n", $value));
    // Remove any non-printable characters except newlines and tabs
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value);
    return $value;
}

function sanitize_html(string $value): string
{
    // Escape for safe HTML output
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sanitize_array(array $input): array
{
    $out = [];
    foreach ($input as $k => $v) {
        if (is_string($v)) {
            $out[$k] = sanitize($v);
        } elseif (is_array($v)) {
            $out[$k] = sanitize_array($v);
        } else {
            $out[$k] = $v;
        }
    }
    return $out;
}

/**
 * Get input safely from request superglobals and optionally cast
 * type: string|int|float|bool|raw
 */
function input(string $key, mixed $default = null, string $type = 'string'): mixed
{
    $val = $_POST[$key] ?? $_GET[$key] ?? $_REQUEST[$key] ?? $default;
    if ($val === null) return $default;

    if ($type === 'raw') return $val;

    if (is_array($val)) {
        // sanitize recursively for arrays
        return sanitize_array($val);
    }

    $val = (string) $val;
    $val = sanitize($val);

    return match ($type) {
        'int' => (int) $val,
        'float' => (float) $val,
        'bool' => filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $val,
        default => $val,
    };
}

// ---------- Actions ----------
function add_action(string $hook, callable $callback, int $priority = 10): void
{
    Hooks::add_action($hook, $callback, $priority);
}

function do_action(string $hook, ...$args): void
{
    Hooks::do_action($hook, ...$args);
}

function remove_action(string $hook, callable $callback, int $priority = 10): bool
{
    return Hooks::remove_action($hook, $callback, $priority);
}

function has_action(string $hook): bool
{
    return Hooks::has_action($hook);
}

// ---------- Filters ----------
function add_filter(string $hook, callable $callback, int $priority = 10): void
{
    Hooks::add_filter($hook, $callback, $priority);
}

function apply_filters(string $hook, mixed $value, ...$args): mixed
{
    return Hooks::apply_filters($hook, $value, ...$args);
}

function remove_filter(string $hook, callable $callback, int $priority = 10): bool
{
    return Hooks::remove_filter($hook, $callback, $priority);
}

function has_filter(string $hook): bool
{
    return Hooks::has_filter($hook);
}

// ---------- Pagination ----------
function paginate(array $items, int $perPage = 5, int $currentPage = 1): array
{
    $totalItems = count($items);
    $totalPages = max(1, (int) ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'items' => array_slice($items, $offset, $perPage, true),
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
    ];
}
