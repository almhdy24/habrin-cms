<?php
/**
 * Theme Manager – Production-ready
 */

declare(strict_types=1);

namespace Models;

use Core\App;

class ThemesModel
{
    private string $themesDir;
    private string $activeTheme;
    private array $allThemes = [];
    private array $errors = [];

    public function __construct()
    {
        $this->themesDir = App::path('themes');
        $this->activeTheme = App::$config['theme'] ?? 'default';
        $this->loadThemes();

        // Validate active theme still exists
        if (!isset($this->allThemes[$this->activeTheme])) {
            $this->activeTheme = 'default';
            // If default also doesn't exist, pick the first available
            if (!isset($this->allThemes['default']) && !empty($this->allThemes)) {
                $this->activeTheme = array_key_first($this->allThemes);
            }
            // Update config to prevent future failures
            App::setConfig('theme', $this->activeTheme);
        }
    }

    private function loadThemes(): void
    {
        $this->allThemes = [];
        if (!is_dir($this->themesDir)) return;

        foreach (scandir($this->themesDir) as $dir) {
            if ($dir === '.' || $dir === '..') continue;
            $themePath = $this->themesDir . '/' . $dir;
            if (!is_dir($themePath)) continue;

            // Require theme.json
            $manifestFile = $themePath . '/theme.json';
            if (!is_file($manifestFile)) {
                $this->errors[$dir] = 'Missing theme.json manifest';
                continue;
            }

            $manifest = json_decode(file_get_contents($manifestFile), true);
            if (!is_array($manifest)) {
                $this->errors[$dir] = 'Invalid theme.json format';
                continue;
            }

            // Validate required fields
            if (empty($manifest['name'])) {
                $this->errors[$dir] = 'Missing "name" in theme.json';
                continue;
            }

            // Validate layouts directory exists
            $layoutsDir = $themePath . '/layouts';
            if (!is_dir($layoutsDir)) {
                $this->errors[$dir] = 'Missing "layouts" directory';
                continue;
            }

            // Version requirements
            if (!empty($manifest['requires']['cms'])) {
                $requiredVersion = ltrim($manifest['requires']['cms'], '>= ');
                if (version_compare(App::CORE_VERSION, $requiredVersion, '<')) {
                    $this->errors[$dir] = 'Requires CMS v' . $requiredVersion . ' or higher';
                    continue;
                }
            }

            $this->allThemes[$dir] = [
                'id' => $dir,
                'name' => $manifest['name'],
                'version' => $manifest['version'] ?? '1.0.0',
                'description' => $manifest['description'] ?? '',
                'author' => $manifest['author'] ?? '',
                'screenshot' => $manifest['screenshot'] ?? '',
                'active' => ($dir === $this->activeTheme),
                'path' => $themePath,
                'layouts' => $this->getLayouts($dir),
                'has_layouts' => !empty($this->getLayouts($dir)),
            ];
        }
    }

    public function getActive(): string
    {
        return $this->activeTheme;
    }

    public function getActiveData(): ?array
    {
        return $this->allThemes[$this->activeTheme] ?? null;
    }

    public function getAll(): array
    {
        return $this->allThemes;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getScreenshot(string $theme): ?string
    {
        if (!isset($this->allThemes[$theme])) return null;
        $data = $this->allThemes[$theme];
        if (empty($data['screenshot'])) return null;

        $screenshotPath = $data['path'] . '/' . $data['screenshot'];
        if (!is_file($screenshotPath)) return null;

        // Validate file extension
        $ext = strtolower(pathinfo($screenshotPath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'])) return null;

        return '/themes/' . $theme . '/' . $data['screenshot'];
    }

    public function getLayouts(string $theme): array
    {
        $layoutsDir = $this->themesDir . '/' . $theme . '/layouts';
        if (!is_dir($layoutsDir)) return [];

        $layouts = [];
        foreach (scandir($layoutsDir) as $file) {
            if ($file === '.' || $file === '..') continue;
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $layouts[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
        return $layouts;
    }

    public function activate(string $theme): bool
    {
        // Re-validate theme exists
        $this->loadThemes();
        if (!isset($this->allThemes[$theme])) {
            $this->errors['activate'] = "Theme '{$theme}' not found.";
            return false;
        }

        // Validate theme has layouts
        if (empty($this->allThemes[$theme]['layouts'])) {
            $this->errors['activate'] = "Theme '{$theme}' has no layouts.";
            return false;
        }

        // Update config safely
        if (!App::setConfig('theme', $theme)) {
            $this->errors['activate'] = "Failed to write config.";
            return false;
        }

        $this->activeTheme = $theme;
        $this->loadThemes(); // Refresh active flag
        return true;
    }

    public function isValid(string $theme): bool
    {
        return isset($this->allThemes[$theme]);
    }
}
