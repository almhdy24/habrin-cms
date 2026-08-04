<?php

declare(strict_types=1);

namespace Core;

abstract class Model
{
    protected string $file;
    protected string $lockFile;
    protected static array $cache = [];

    final public function __construct()
    {
        if (method_exists($this, 'boot')) {
            $this->boot();
        }

        if (empty($this->file)) {
            throw new \RuntimeException(
                static::class . ' must define a $file property'
            );
        }

        $this->lockFile = $this->file . '.lock';

        if (!file_exists($this->file)) {
            $this->initializeFile();
        }
    }

    protected function initializeFile(): void
    {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $this->atomicWrite('{}');
    }

    /**
     * Acquire exclusive lock and execute callback
     * Always reads fresh data inside the lock
     */
    protected function withLock(callable $callback): mixed
    {
        $lockFp = fopen($this->lockFile, 'c');
        if ($lockFp === false) {
            throw new \RuntimeException("Unable to open lock file: {$this->lockFile}");
        }

        if (!flock($lockFp, LOCK_EX)) {
            fclose($lockFp);
            throw new \RuntimeException("Unable to acquire lock: {$this->lockFile}");
        }

        try {
            // Clear cache before reading inside lock
            $cacheKey = $this->file;
            unset(static::$cache[$cacheKey]);

            $result = $callback();
        } finally {
            flock($lockFp, LOCK_UN);
            fclose($lockFp);
        }

        return $result;
    }

    /**
     * Atomic write using temp file + rename
     */
    protected function atomicWrite(string $json): bool
    {
        $temp = $this->file . '.tmp.' . uniqid();

        if (file_put_contents($temp, $json, LOCK_EX) === false) {
            @unlink($temp);
            return false;
        }

        // Ensure the temp file is written before rename
        clearstatcache(true, $temp);

        if (!rename($temp, $this->file)) {
            @unlink($temp);
            return false;
        }

        return true;
    }

    /**
     * Read all data with cache (used outside locks)
     */
    public function all(): array
    {
        $cacheKey = $this->file;
        if (isset(static::$cache[$cacheKey])) {
            return static::$cache[$cacheKey];
        }

        $json = @file_get_contents($this->file);
        if ($json === false) {
            return [];
        }

        $data = json_decode($json, true);
        $data = is_array($data) ? $data : [];
        static::$cache[$cacheKey] = $data;
        return $data;
    }

    /**
     * Fresh read inside lock
     */
    protected function freshAll(): array
    {
        $cacheKey = $this->file;
        unset(static::$cache[$cacheKey]);

        $json = @file_get_contents($this->file);
        if ($json === false) {
            return [];
        }

        $data = json_decode($json, true);
        $data = is_array($data) ? $data : [];
        static::$cache[$cacheKey] = $data;
        return $data;
    }

    public function find(string|int $id): mixed
    {
        $items = $this->all();
        return $items[$id] ?? null;
    }

    /**
     * Save data atomically with lock (single record)
     */
    final public function save(array $data): bool
    {
        return $this->withLock(function () use ($data) {
            $json = json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            if ($json === false) {
                return false;
            }

            $success = $this->atomicWrite($json);

            if ($success) {
                $cacheKey = $this->file;
                unset(static::$cache[$cacheKey]);
            }

            return $success;
        });
    }

    /**
     * Bulk save – writes data directly inside lock
     * For seeding/import only
     */
    final public function bulkSave(array $data): bool
    {
        return $this->withLock(function () use ($data) {
            $json = json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            if ($json === false) {
                return false;
            }

            $success = $this->atomicWrite($json);

            if ($success) {
                $cacheKey = $this->file;
                unset(static::$cache[$cacheKey]);
            }

            return $success;
        });
    }

    public static function clearCache(): void
    {
        static::$cache = [];
    }
}
