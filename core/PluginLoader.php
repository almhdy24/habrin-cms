<?php

declare(strict_types=1);

namespace Core;

final class PluginLoader
{
    public function __construct(
        private readonly string $pluginPath
    ) {}

    public function discover(): array
    {
        $plugins = [];

        if (!is_dir($this->pluginPath)) {
            return $plugins;
        }

        foreach (scandir($this->pluginPath) as $file) {
            if ($file === '.' || $file === '..') continue;

            $fullPath = $this->pluginPath . DIRECTORY_SEPARATOR . $file;

            if (is_dir($fullPath)) {
                $manifestFile = $fullPath . '/plugin.json';
                if (is_file($manifestFile)) {
                    $manifest = json_decode((string) file_get_contents($manifestFile), true);
                    if (is_array($manifest) && isset($manifest['name'], $manifest['entry']) && is_string($manifest['entry'])) {
                        $entry = trim($manifest['entry']);
                        // Reject directory traversal sequences
                        if (!str_contains($entry, '..') && !str_starts_with($entry, '/') && !str_starts_with($entry, '\\')) {
                            $entryFile = $fullPath . DIRECTORY_SEPARATOR . $entry;
                            $realEntry = realpath($entryFile);
                            $realDir = realpath($fullPath);
                            if ($realEntry !== false && $realDir !== false && str_starts_with($realEntry, $realDir . DIRECTORY_SEPARATOR) && is_file($realEntry)) {
                                $plugins[$manifest['name']] = [
                                    'file' => $realEntry,
                                    'manifest' => $manifest,
                                ];
                            }
                        }
                    }
                }
            } else {
                // Single-file plugin
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $name = pathinfo($file, PATHINFO_FILENAME);
                    $plugins[$name] = [
                        'file' => $fullPath,
                        'manifest' => [
                            'name' => $name,
                            'description' => '',
                            'version' => '1.0.0',
                            'entry' => $file,
                        ],
                    ];
                }
            }
        }

        return $plugins;
    }
}
