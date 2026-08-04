<?php

declare(strict_types=1);

namespace Core;

abstract class Controller
{
    public function __construct()
    {
        Session::start();
    }

    protected function view(string $view, array $data = []): void
    {
        Hooks::do_action('before_render', $view, $data);
        try {
            View::render($view, $data);
        } finally {
            Hooks::do_action('after_render', $view, $data);
        }
    }

    protected function model(string $model): object
    {
        if (str_contains($model, '\\')) {
            $class = $model;
        } else {
            $class = 'Models\\' . $model;
        }
        if (!class_exists($class)) {
            throw new \RuntimeException("Model not found: {$class}");
        }
        return new $class();
    }

    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    protected function getFlash(): array
    {
        return Session::getFlash();
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function redirect(string $path): never
    {
        header("Location: {$path}");
        exit;
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
