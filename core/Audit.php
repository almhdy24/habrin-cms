<?php

declare(strict_types=1);

namespace Core;

final class Audit
{
    private const AUDIT_LOG = 'logs/audit.log';

    public static function log(string $actor, string $action, array $meta = []): void
    {
        $entry = [
            'time' => date('c'),
            'actor' => $actor,
            'action' => $action,
            'meta' => $meta,
        ];

        $logLine = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";

        $path = App::path(self::AUDIT_LOG);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        file_put_contents($path, $logLine, FILE_APPEND | LOCK_EX);
    }
}
