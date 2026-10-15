<?php

declare(strict_types=1);

namespace Models;

use Core\App;

/**
 * MediaModel - Secure Media Uploads and Management
 */
class MediaModel
{
    public const MAX_FILE_SIZE = 5242880; // 5 MB
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = App::path('assets', 'uploads');
        $this->ensureUploadDirectory();
    }

    private function ensureUploadDirectory(): void
    {
        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0755, true);
        }

        $htaccess = $this->uploadDir . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents(
                $htaccess,
                "<IfModule mod_php.c>\n    php_flag engine off\n</IfModule>\n<FilesMatch \"\.(php|phtml|phar|sh|pl|py|cgi)$\">\n    Require all denied\n</FilesMatch>\n",
                LOCK_EX
            );
        }
    }

    public function getAll(): array
    {
        $items = [];
        if (!is_dir($this->uploadDir)) {
            return $items;
        }

        $files = scandir($this->uploadDir, SCANDIR_SORT_DESCENDING);
        if ($files === false) {
            return $items;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.htaccess' || $file === '.gitkeep') {
                continue;
            }

            $path = $this->uploadDir . DIRECTORY_SEPARATOR . $file;
            if (!is_file($path)) {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                continue;
            }

            $size = (int) filesize($path);
            $time = (int) filemtime($path);
            $mime = (string) (mime_content_type($path) ?: 'application/octet-stream');

            $items[] = [
                'name' => $file,
                'url' => '/assets/uploads/' . rawurlencode($file),
                'size_bytes' => $size,
                'size_formatted' => $this->formatBytes($size),
                'mime' => $mime,
                'is_image' => str_starts_with($mime, 'image/'),
                'created_at' => $time,
            ];
        }

        return $items;
    }

    public function upload(array $file): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Invalid file upload parameter.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => $this->codeToMessage($file['error'])];
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'File size exceeds maximum allowed limit (5MB).'];
        }

        $originalName = (string) ($file['name'] ?? 'file');
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            return ['success' => false, 'error' => "File extension '.{$ext}' is not permitted."];
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        if (!is_readable($tmpPath)) {
            return ['success' => false, 'error' => 'Uploaded temporary file could not be read.'];
        }

        // Verify MIME type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmpPath) : mime_content_type($tmpPath);
        if ($finfo) {
            finfo_close($finfo);
        }

        // Normalize text/xml or image/svg to image/svg+xml for SVG
        if ($ext === 'svg' && in_array($mime, ['text/plain', 'text/xml', 'text/html', 'image/svg', 'image/svg+xml'], true)) {
            $mime = 'image/svg+xml';
        }

        if (!in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            return ['success' => false, 'error' => "Disallowed MIME type: {$mime}."];
        }

        // Strict SVG content inspection to prevent XSS
        if ($ext === 'svg' && !$this->sanitizeSvg($tmpPath)) {
            return ['success' => false, 'error' => 'SVG file contains disallowed script elements or event handlers.'];
        }

        // Generate safe, unique filename
        $cleanBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $cleanBase = substr((string) $cleanBase, 0, 40);
        if ($cleanBase === '') {
            $cleanBase = 'upload';
        }

        $targetName = time() . '_' . bin2hex(random_bytes(4)) . '_' . $cleanBase . '.' . $ext;
        $targetPath = $this->uploadDir . DIRECTORY_SEPARATOR . $targetName;

        // Support both real HTTP upload and CLI testing
        $moved = false;
        if (is_uploaded_file($tmpPath)) {
            $moved = move_uploaded_file($tmpPath, $targetPath);
        } else {
            $moved = copy($tmpPath, $targetPath);
        }

        if (!$moved) {
            return ['success' => false, 'error' => 'Failed to save uploaded file to destination.'];
        }

        chmod($targetPath, 0644);

        return [
            'success' => true,
            'filename' => $targetName,
            'url' => '/assets/uploads/' . rawurlencode($targetName),
            'markdown' => '![' . htmlspecialchars($cleanBase, ENT_QUOTES, 'UTF-8') . '](/assets/uploads/' . rawurlencode($targetName) . ')',
        ];
    }

    public function delete(string $filename): bool
    {
        $filename = basename($filename);
        if ($filename === '' || $filename === '.htaccess' || $filename === '.gitkeep') {
            return false;
        }

        $path = $this->uploadDir . DIRECTORY_SEPARATOR . $filename;
        if (is_file($path)) {
            return @unlink($path);
        }

        return false;
    }

    private function sanitizeSvg(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        // Reject script tags, foreignObject, iframe, embed, object
        $disallowedTags = ['script', 'foreignobject', 'iframe', 'object', 'embed', 'applet', 'meta', 'link'];
        foreach ($disallowedTags as $tag) {
            if (preg_match('/<\s*' . $tag . '\b/i', $content)) {
                return false;
            }
        }

        // Reject inline JavaScript event handlers (e.g. onload, onerror, onclick)
        if (preg_match('/\bon[a-z]+\s*=/i', $content)) {
            return false;
        }

        // Reject javascript: URLs
        if (preg_match('/href\s*=\s*["\']?\s*javascript:/i', $content)) {
            return false;
        }

        return true;
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

    private function codeToMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            default => 'Unknown upload error.',
        };
    }
}
