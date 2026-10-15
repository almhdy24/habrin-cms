<?php

declare(strict_types=1);

namespace Models;

use Core\App;
use ZipArchive;

/**
 * BackupModel - Content Backup, Export, and Restoration
 */
class BackupModel
{
    private string $backupDir;
    private string $contentDir;

    public function __construct()
    {
        $this->backupDir = App::path('backups');
        $this->contentDir = App::path('content');
        $this->ensureDirectories();
    }

    private function ensureDirectories(): void
    {
        if (!is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0755, true);
        }

        $htaccess = $this->backupDir . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents(
                $htaccess,
                "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order deny,allow\n    Deny from all\n</IfModule>\n",
                LOCK_EX
            );
        }
    }

    public function getAll(): array
    {
        $backups = [];
        if (!is_dir($this->backupDir)) {
            return $backups;
        }

        $files = scandir($this->backupDir, SCANDIR_SORT_DESCENDING);
        if ($files === false) {
            return $backups;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.htaccess' || $file === '.gitkeep') {
                continue;
            }

            $path = $this->backupDir . DIRECTORY_SEPARATOR . $file;
            if (!is_file($path)) {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['zip', 'json'], true)) {
                continue;
            }

            $size = (int) filesize($path);
            $time = (int) filemtime($path);

            $backups[] = [
                'filename' => $file,
                'size_bytes' => $size,
                'size_formatted' => $this->formatBytes($size),
                'created_at' => $time,
                'type' => $ext,
            ];
        }

        return $backups;
    }

    public function create(): array
    {
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "backup-{$timestamp}.zip";
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return ['success' => false, 'error' => 'Could not create ZIP backup file.'];
            }

            $this->addDirectoryToZip($zip, $this->contentDir, 'content');
            $zip->close();
        } else {
            // Fallback to structured JSON dump if ZipArchive is missing
            $filename = "backup-{$timestamp}.json";
            $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

            $dump = [
                'created_at' => time(),
                'version' => App::CORE_VERSION,
                'files' => $this->readDirectoryRecursive($this->contentDir),
            ];

            if (file_put_contents($filepath, json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) === false) {
                return ['success' => false, 'error' => 'Could not write JSON backup file.'];
            }
        }

        chmod($filepath, 0600);

        return [
            'success' => true,
            'filename' => $filename,
            'size' => filesize($filepath),
        ];
    }

    public function delete(string $filename): bool
    {
        $filename = basename($filename);
        if ($filename === '' || $filename === '.htaccess' || $filename === '.gitkeep') {
            return false;
        }

        $path = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
        if (is_file($path)) {
            return @unlink($path);
        }

        return false;
    }

    public function restore(array $uploadedFile): array
    {
        if (!isset($uploadedFile['error']) || $uploadedFile['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Upload error occurred during restore.'];
        }

        $tmpPath = (string) ($uploadedFile['tmp_name'] ?? '');
        $origName = (string) ($uploadedFile['name'] ?? '');
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['zip', 'json'], true)) {
            return ['success' => false, 'error' => 'Invalid backup archive format. Only .zip and .json are accepted.'];
        }

        // Create safety backup of current content before restoring
        $this->create();

        if ($ext === 'zip' && class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($tmpPath) !== true) {
                return ['success' => false, 'error' => 'Corrupted or unreadable ZIP archive.'];
            }

            // Verify zip contains no path traversal entries (e.g. ../)
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = $stat['name'] ?? '';
                if (str_contains($name, '..') || str_starts_with($name, '/') || str_starts_with($name, '\\')) {
                    $zip->close();
                    return ['success' => false, 'error' => 'Archive contains unsafe file paths.'];
                }
            }

            // Extract to temporary folder then copy content
            $tempExtract = $this->backupDir . DIRECTORY_SEPARATOR . 'restore_' . uniqid();
            @mkdir($tempExtract, 0700, true);
            $zip->extractTo($tempExtract);
            $zip->close();

            $sourceContent = is_dir($tempExtract . '/content') ? $tempExtract . '/content' : $tempExtract;
            $this->copyRecursive($sourceContent, $this->contentDir);

            // Clean up temp extract
            $this->removeRecursive($tempExtract);
        } elseif ($ext === 'json') {
            $json = file_get_contents($tmpPath);
            $data = $json ? json_decode($json, true) : null;
            if (!is_array($data) || !isset($data['files']) || !is_array($data['files'])) {
                return ['success' => false, 'error' => 'Invalid JSON backup format.'];
            }

            foreach ($data['files'] as $relativePath => $content) {
                if (str_contains($relativePath, '..') || str_starts_with($relativePath, '/')) {
                    continue;
                }
                $dest = $this->contentDir . DIRECTORY_SEPARATOR . $relativePath;
                $dir = dirname($dest);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                file_put_contents($dest, (string) $content, LOCK_EX);
            }
        } else {
            return ['success' => false, 'error' => 'ZIP restoration not supported in this environment.'];
        }

        return ['success' => true];
    }

    private function addDirectoryToZip(ZipArchive $zip, string $dir, string $zipPath): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || str_ends_with($file, '.lock') || str_ends_with($file, '.tmp')) {
                continue;
            }

            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            $localPath = $zipPath . '/' . $file;

            if (is_dir($filePath)) {
                $zip->addEmptyDir($localPath);
                $this->addDirectoryToZip($zip, $filePath, $localPath);
            } elseif (is_file($filePath)) {
                $zip->addFile($filePath, $localPath);
            }
        }
    }

    private function readDirectoryRecursive(string $dir, string $prefix = ''): array
    {
        $files = [];
        if (!is_dir($dir)) return $files;

        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..' || str_ends_with($file, '.lock') || str_ends_with($file, '.tmp')) {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            $rel = $prefix !== '' ? $prefix . '/' . $file : $file;
            if (is_dir($path)) {
                $files = array_merge($files, $this->readDirectoryRecursive($path, $rel));
            } elseif (is_file($path)) {
                $files[$rel] = file_get_contents($path);
            }
        }
        return $files;
    }

    private function copyRecursive(string $src, string $dst): void
    {
        if (!is_dir($src)) return;
        if (!is_dir($dst)) @mkdir($dst, 0755, true);

        foreach (scandir($src) as $file) {
            if ($file === '.' || $file === '..') continue;
            $srcPath = $src . DIRECTORY_SEPARATOR . $file;
            $dstPath = $dst . DIRECTORY_SEPARATOR . $file;
            if (is_dir($srcPath)) {
                $this->copyRecursive($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
    }

    private function removeRecursive(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->removeRecursive($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
