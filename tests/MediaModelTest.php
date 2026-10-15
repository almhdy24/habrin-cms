<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Models\MediaModel;

final class MediaModelTest extends TestCase
{
    private MediaModel $model;
    private array $tempFiles = [];

    protected function setUp(): void
    {
        $this->model = new MediaModel();
        $this->tempFiles = [];
    }

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function createTempFile(string $content, string $suffix = '.tmp'): string
    {
        $path = tempnam(sys_get_temp_dir(), 'test_media_') . $suffix;
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;
        return $path;
    }

    public function testSuccessfulImageUploadAndDelete(): void
    {
        // 1x1 transparent PNG binary
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAA=', true);
        $tmp = $this->createTempFile($pngData, '.png');

        $result = $this->model->upload([
            'name'     => 'test_avatar.png',
            'type'     => 'image/png',
            'tmp_name' => $tmp,
            'error'    => UPLOAD_ERR_OK,
            'size'     => strlen($pngData),
        ]);

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['filename']);
        $this->assertStringStartsWith('/assets/uploads/', $result['url']);

        // Verify it appears in getAll()
        $all = $this->model->getAll();
        $found = array_filter($all, fn($item) => $item['name'] === $result['filename']);
        $this->assertNotEmpty($found);

        // Delete the uploaded file
        $deleted = $this->model->delete($result['filename']);
        $this->assertTrue($deleted);
    }

    public function testRejectsDisallowedExtension(): void
    {
        $phpCode = '<?php echo "evil";';
        $tmp = $this->createTempFile($phpCode, '.php');

        $result = $this->model->upload([
            'name'     => 'backdoor.php',
            'type'     => 'application/x-php',
            'tmp_name' => $tmp,
            'error'    => UPLOAD_ERR_OK,
            'size'     => strlen($phpCode),
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString("File extension '.php' is not permitted", $result['error']);
    }

    public function testRejectsMimeTypeMismatch(): void
    {
        $phpCode = '<?php phpinfo(); ?>';
        $tmp = $this->createTempFile($phpCode, '.jpg');

        $result = $this->model->upload([
            'name'     => 'fake_image.jpg',
            'type'     => 'image/jpeg',
            'tmp_name' => $tmp,
            'error'    => UPLOAD_ERR_OK,
            'size'     => strlen($phpCode),
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Disallowed MIME type', $result['error']);
    }

    public function testRejectsOversizedFile(): void
    {
        $tmp = $this->createTempFile('small');

        $result = $this->model->upload([
            'name'     => 'huge.png',
            'type'     => 'image/png',
            'tmp_name' => $tmp,
            'error'    => UPLOAD_ERR_OK,
            'size'     => MediaModel::MAX_FILE_SIZE + 1024,
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('exceeds maximum allowed limit', $result['error']);
    }

    public function testRejectsMaliciousSvgScript(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.cookie)</script><circle r="10"/></svg>';
        $tmp = $this->createTempFile($svg, '.svg');

        $result = $this->model->upload([
            'name'     => 'malicious.svg',
            'type'     => 'image/svg+xml',
            'tmp_name' => $tmp,
            'error'    => UPLOAD_ERR_OK,
            'size'     => strlen($svg),
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('disallowed script elements', $result['error']);
    }

    public function testRejectsMaliciousSvgOnload(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><circle r="10"/></svg>';
        $tmp = $this->createTempFile($svg, '.svg');

        $result = $this->model->upload([
            'name'     => 'onload_attack.svg',
            'type'     => 'image/svg+xml',
            'tmp_name' => $tmp,
            'error'    => UPLOAD_ERR_OK,
            'size'     => strlen($svg),
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('disallowed script elements', $result['error']);
    }

    public function testAllowsCleanSvg(): void
    {
        $cleanSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="50" cy="50" r="40" stroke="green" stroke-width="4" fill="yellow" /></svg>';
        $tmp = $this->createTempFile($cleanSvg, '.svg');

        $result = $this->model->upload([
            'name'     => 'clean_icon.svg',
            'type'     => 'image/svg+xml',
            'tmp_name' => $tmp,
            'error'    => UPLOAD_ERR_OK,
            'size'     => strlen($cleanSvg),
        ]);

        $this->assertTrue($result['success']);
        $this->model->delete($result['filename']);
    }

    public function testDeleteRejectsSystemFilesAndTraversal(): void
    {
        $this->assertFalse($this->model->delete('.htaccess'));
        $this->assertFalse($this->model->delete('.gitkeep'));
        $this->assertFalse($this->model->delete('../../config.php'));
    }
}
