<?php

/**
 * Pico JSON CMS — Global Functions
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
