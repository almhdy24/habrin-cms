<?php

declare(strict_types=1);

namespace Core;

use League\Plates\Exception\TemplateNotFound;
use League\Plates\Template\Name;
use League\Plates\Template\ResolveTemplatePath;

final class ThemeTemplateResolver implements ResolveTemplatePath
{
    public function __construct(
        private readonly string $themePath,
        private readonly string $defaultPath
    ) {}

    public function __invoke(Name $name): string
    {
        $template = $this->normalizeTemplateName($name);

        // 1. Try active theme
        $activeFile = $this->themePath . DIRECTORY_SEPARATOR . $template;
        if (is_file($activeFile)) {
            return $activeFile;
        }

        // 2. Fallback to default theme
        $defaultFile = $this->defaultPath . DIRECTORY_SEPARATOR . $template;
        if (is_file($defaultFile)) {
            return $defaultFile;
        }

        throw new TemplateNotFound($name->getName());
    }

    private function normalizeTemplateName(Name $name): string
    {
        // Use getName() – not string casting
        $template = $name->getName();

        // Normalize separators
        $template = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $template);

        // Reject absolute paths
        if (
            str_starts_with($template, DIRECTORY_SEPARATOR) ||
            preg_match('/^[a-zA-Z]:[\\\\\/]/', $template)
        ) {
            throw new \InvalidArgumentException('Invalid absolute template path.');
        }

        // Prevent directory traversal
        $parts = explode(DIRECTORY_SEPARATOR, $template);
        foreach ($parts as $part) {
            if ($part === '..') {
                throw new \InvalidArgumentException('Invalid template path.');
            }
        }

        // Empty check
        if ($template === '') {
            throw new \InvalidArgumentException('Template name cannot be empty.');
        }

        // Append .php if no extension
        if (pathinfo($template, PATHINFO_EXTENSION) !== 'php') {
            $template .= '.php';
        }

        return $template;
    }
}
