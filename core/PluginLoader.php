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
                    $manifest = json_decode(file_get_contents($manifestFile), true);
                    if (is_array($manifest) && isset($manifest['name'], $manifest['entry'])) {
                        $entryFile = $fullPath . '/' . $manifest['entry'];
                        if (is_file($entryFile)) {
                            $plugins[$manifest['name']] = [
                                'file' => $entryFile,
                                'manifest' => $manifest,
                            ];
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
