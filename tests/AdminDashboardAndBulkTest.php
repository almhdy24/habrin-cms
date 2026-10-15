<?php

declare(strict_types=1);

namespace Tests;

use Core\App;
use Core\I18n;
use Core\Session;
use Models\PostsModel;
use PHPUnit\Framework\TestCase;

class AdminDashboardAndBulkTest extends TestCase
{
    private PostsModel $posts;
    private string $testContentDir;

    protected function setUp(): void
    {
        parent::setUp();
        Session::start();
        $_SESSION['is_admin'] = true;
        $_SESSION['user'] = 'admin';
        $_SESSION['csrf_token'] = 'valid_test_token';

        $this->testContentDir = BASE_PATH . '/content';
        $this->posts = new PostsModel();
        $this->posts->clearCache();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    public function testBulkPublishDraftAndTrashLifecycle(): void
    {
        // 1. Create 3 test posts
        $p1 = $this->posts->nextId();
        $this->posts->savePost($p1, [
            'id' => $p1,
            'title' => 'Bulk Test 1',
            'slug' => 'bulk-test-1-' . time(),
            'content' => 'Content 1',
            'status' => 'draft',
            'created_at' => time(),
            'updated_at' => time(),
            'deleted_at' => null,
        ]);

        $p2 = $this->posts->nextId();
        $this->posts->savePost($p2, [
            'id' => $p2,
            'title' => 'Bulk Test 2',
            'slug' => 'bulk-test-2-' . time(),
            'content' => 'Content 2',
            'status' => 'draft',
            'created_at' => time(),
            'updated_at' => time(),
            'deleted_at' => null,
        ]);

        $p3 = $this->posts->nextId();
        $this->posts->savePost($p3, [
            'id' => $p3,
            'title' => 'Bulk Test 3',
            'slug' => 'bulk-test-3-' . time(),
            'content' => 'Content 3',
            'status' => 'draft',
            'created_at' => time(),
            'updated_at' => time(),
            'deleted_at' => null,
        ]);

        $postIds = [$p1, $p2, $p3];

        // 2. Perform bulk publish
        foreach ($postIds as $id) {
            $post = $this->posts->find($id);
            $this->assertNotNull($post);
            $post['status'] = 'published';
            $post['updated_at'] = time();
            $this->posts->savePost($id, $post);
        }

        $this->posts->clearCache();
        foreach ($postIds as $id) {
            $post = $this->posts->find($id);
            $this->assertSame('published', $post['status']);
            $this->assertNull($post['deleted_at']);
        }

        // 3. Perform bulk draft
        foreach ($postIds as $id) {
            $post = $this->posts->find($id);
            $post['status'] = 'draft';
            $post['updated_at'] = time();
            $this->posts->savePost($id, $post);
        }

        $this->posts->clearCache();
        foreach ($postIds as $id) {
            $post = $this->posts->find($id);
            $this->assertSame('draft', $post['status']);
        }

        // 4. Perform bulk move to trash
        foreach ($postIds as $id) {
            $post = $this->posts->find($id);
            $post['deleted_at'] = time();
            $this->posts->savePost($id, $post);
        }

        $this->posts->clearCache();
        foreach ($postIds as $id) {
            $post = $this->posts->find($id);
            $this->assertNotNull($post['deleted_at']);
        }

        // Clean up
        foreach ($postIds as $id) {
            $this->posts->deletePost($id);
        }
    }

    public function testRequiredExtensionsLoaded(): void
    {
        $this->assertTrue(extension_loaded('json'), 'PHP json extension must be enabled');
        $this->assertTrue(extension_loaded('mbstring'), 'PHP mbstring extension must be enabled');
        $this->assertTrue(extension_loaded('fileinfo'), 'PHP fileinfo extension must be enabled');
    }

    public function testStorageDirectoriesAreWritable(): void
    {
        $contentPath = App::path('content');
        $logsPath = App::path('logs');
        $backupsPath = App::path('backups');

        $this->assertTrue(is_dir($contentPath), 'Content dir must exist');
        $this->assertTrue(is_writable($contentPath), 'Content dir must be writable');

        $this->assertTrue(is_dir($logsPath), 'Logs dir must exist');
        $this->assertTrue(is_writable($logsPath), 'Logs dir must be writable');

        $this->assertTrue(is_dir($backupsPath), 'Backups dir must exist');
        $this->assertTrue(is_writable($backupsPath), 'Backups dir must be writable');
    }
}
