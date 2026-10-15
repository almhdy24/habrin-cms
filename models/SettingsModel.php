<?php

declare(strict_types=1);

namespace Models;

use Core\App;
use Core\Model;

class SettingsModel extends Model
{
    protected string $file;
    private array $defaults = [
        'site_title' => 'Habrin CMS',
        'site_description' => 'Publish without the clutter.',
        'site_keywords' => 'habrin, cms, php, json, blog, markdown, publishing',
        'hero_heading' => 'Welcome to Habrin CMS',
        'hero_description' => 'Publish without the clutter. Fast, private, and database-free.',
        'hero_button_text' => 'Get Started',
        'hero_button_link' => '/about',
        'meta' => []
    ];

    protected function boot(): void
    {
        $this->file = App::path('content', 'settings.json');
        $this->ensureDefaults();
    }

    private function ensureDefaults(): void
    {
        $current = $this->all();
        $merged = array_merge($this->defaults, $current);
        if ($merged !== $current) {
            $this->save($merged);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    public function set(string $key, mixed $value): bool
    {
        $settings = $this->all();
        $settings[$key] = $value;
        return $this->save($settings);
    }

    public function setMany(array $values): bool
    {
        $settings = array_merge($this->all(), $values);
        return $this->save($settings);
    }

    public function all(): array
    {
        return array_merge($this->defaults, parent::all());
    }
}
