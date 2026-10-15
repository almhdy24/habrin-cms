<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Models\BackupModel;

final class BackupModelTest extends TestCase
{
    private BackupModel $model;
    private array $createdBackups = [];
    private array $tempFiles = [];

    protected function setUp(): void
    {
        $this->model = new BackupModel();
        $this->createdBackups = [];
        $this->tempFiles = [];
    }

    protected function tearDown(): void
    {
        foreach ($this->createdBackups as $filename) {
            $this->model->delete($filename);
        }

        foreach ($this->tempFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function createTempFile(string $content, string $suffix = '.tmp'): string
    {
        $path = tempnam(sys_get_temp_dir(), 'test_backup_') . $suffix;
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;
        return $path;
    }

    public function testCreateBackupAndList(): void
    {
        $result = $this->model->create();
        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['filename']);
        $this->assertGreaterThan(0, $result['size']);

        $this->createdBackups[] = $result['filename'];

        $all = $this->model->getAll();
        $found = array_filter($all, fn($b) => $b['filename'] === $result['filename']);
        $this->assertNotEmpty($found);
    }

    public function testDeleteBackup(): void
    {
        $result = $this->model->create();
        $this->assertTrue($result['success']);

        $deleted = $this->model->delete($result['filename']);
        $this->assertTrue($deleted);

        $all = $this->model->getAll();
        $found = array_filter($all, fn($b) => $b['filename'] === $result['filename']);
        $this->assertEmpty($found);
    }

    public function testRestoreRejectsInvalidExtensions(): void
    {
        $tmp = $this->createTempFile('not a real archive', '.tar.gz');

        $result = $this->model->restore([
            'name'     => 'archive.tar.gz',
            'tmp_name' => $tmp,
            'error'    => UPLOAD_ERR_OK,
            'size'     => 100,
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid backup archive format', $result['error']);
    }

    public function testRestoreRejectsZipWithPathTraversal(): void
    {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive extension is not available.');
        }

        $zipPath = $this->createTempFile('', '.zip');
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $zip->addFromString('../../../etc/malicious.txt', 'malicious content');
        $zip->close();

        $result = $this->model->restore([
            'name'     => 'traversal.zip',
            'tmp_name' => $zipPath,
            'error'    => UPLOAD_ERR_OK,
            'size'     => filesize($zipPath),
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('unsafe file paths', $result['error']);
    }

    public function testDeleteRejectsSystemFiles(): void
    {
        $this->assertFalse($this->model->delete('.htaccess'));
        $this->assertFalse($this->model->delete('.gitkeep'));
        $this->assertFalse($this->model->delete('../config.php'));
    }
}
