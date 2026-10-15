<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Models\PostsModel;

final class PostsModelTest extends TestCase
{
    private PostsModel $model;
    private array $createdIds = [];

    protected function setUp(): void
    {
        $this->model = new PostsModel();
        $this->createdIds = [];
    }

    protected function tearDown(): void
    {
        foreach ($this->createdIds as $id) {
            $this->model->deletePost($id);
        }
    }

    public function testSaveAndFindPost(): void
    {
        $id = $this->model->nextId();
        $this->createdIds[] = $id;

        $slug = 'test-post-' . $id;
        $postData = [
            'title'      => 'Test Post ' . $id,
            'slug'       => $slug,
            'content'    => 'This is a test post body.',
            'created_at' => time(),
            'updated_at' => time(),
            'status'     => 'published',
        ];

        $saved = $this->model->savePost($id, $postData);
        $this->assertTrue($saved);

        $found = $this->model->find($id);
        $this->assertNotNull($found);
        $this->assertSame('Test Post ' . $id, $found['title']);

        $bySlug = $this->model->findBySlug($slug);
        $this->assertNotNull($bySlug);
        $this->assertSame($id, (int) $bySlug['id']);
    }

    public function testSlugGenerationAndCollision(): void
    {
        $id1 = $this->model->nextId();
        $this->createdIds[] = $id1;

        $title = 'Unique Test Article Title ' . uniqid();
        $slug1 = $this->model->generateSlug($title);
        $this->assertNotEmpty($slug1);

        $this->model->savePost($id1, [
            'title'      => $title,
            'slug'       => $slug1,
            'content'    => 'Body 1',
            'created_at' => time(),
            'status'     => 'published',
        ]);

        // Second post with the exact same title should collide and get a suffix
        $slug2 = $this->model->generateSlug($title);
        $this->assertNotSame($slug1, $slug2);
        $this->assertSame($slug1 . '-2', $slug2);

        // Generating slug for the same post with ignoreId should preserve the slug
        $slugSame = $this->model->generateSlug($title, $id1);
        $this->assertSame($slug1, $slugSame);
    }

    public function testDraftWorkflow(): void
    {
        $id = $this->model->nextId();
        $this->createdIds[] = $id;

        $slug = 'draft-post-' . $id;
        $draftData = [
            'title'      => 'Draft Post ' . $id,
            'slug'       => $slug,
            'content'    => 'Draft content that should not be public.',
            'created_at' => time(),
            'updated_at' => time(),
            'status'     => 'draft',
            'draft'      => true,
        ];

        $this->model->savePost($id, $draftData);

        // Draft should NOT be visible to regular visitors
        $publicFind = $this->model->findBySlug($slug, false);
        $this->assertNull($publicFind);

        // Draft SHOULD be visible to authenticated admins
        $adminFind = $this->model->findBySlug($slug, true);
        $this->assertNotNull($adminFind);
        $this->assertSame($id, (int) $adminFind['id']);

        // Check draft counter and list
        $this->assertGreaterThanOrEqual(1, $this->model->draftsCount());
        $drafts = $this->model->drafts();
        $this->assertArrayHasKey((string) $id, $drafts);
    }

    public function testPublishDraft(): void
    {
        $id = $this->model->nextId();
        $this->createdIds[] = $id;

        $slug = 'draft-to-publish-' . $id;
        $this->model->savePost($id, [
            'title'      => 'Draft to Publish',
            'slug'       => $slug,
            'content'    => 'Draft body',
            'created_at' => time(),
            'status'     => 'draft',
            'draft'      => true,
        ]);

        $initialPublished = $this->model->publishedCount();

        // Publish it
        $this->model->savePost($id, [
            'title'      => 'Draft to Publish',
            'slug'       => $slug,
            'content'    => 'Draft body published',
            'created_at' => time(),
            'status'     => 'published',
            'draft'      => false,
        ]);

        $this->assertSame($initialPublished + 1, $this->model->publishedCount());
        $this->assertNotNull($this->model->findBySlug($slug, false));
    }

    public function testSearchPosts(): void
    {
        $id = $this->model->nextId();
        $this->createdIds[] = $id;

        $uniqueKeyword = 'xylophone_' . bin2hex(random_bytes(4));
        $this->model->savePost($id, [
            'title'      => 'Special Instrument ' . $uniqueKeyword,
            'slug'       => 'special-instrument-' . $id,
            'content'    => 'Body of post',
            'created_at' => time(),
            'status'     => 'published',
        ]);

        $results = $this->model->search($uniqueKeyword, 1, 10, true);
        $this->assertGreaterThanOrEqual(1, $results['total']);
        $this->assertSame($id, (int) $results['items'][0]['id']);
    }

    public function testTrashAndRestore(): void
    {
        $id = $this->model->nextId();
        $this->createdIds[] = $id;

        $this->model->savePost($id, [
            'title'      => 'Post to Trash ' . $id,
            'slug'       => 'post-to-trash-' . $id,
            'content'    => 'Content',
            'created_at' => time(),
            'status'     => 'published',
        ]);

        // Trash the post
        $post = $this->model->find($id);
        $post['deleted_at'] = time();
        $this->model->savePost($id, $post);

        $trashed = $this->model->trashed();
        $this->assertArrayHasKey((string) $id, $trashed);

        // Restore the post
        $post['deleted_at'] = null;
        $this->model->savePost($id, $post);

        $trashedAfter = $this->model->trashed();
        $this->assertArrayNotHasKey((string) $id, $trashedAfter);
    }

    public function testDeletePostPermanently(): void
    {
        $id = $this->model->nextId();
        $slug = 'post-to-delete-' . $id;

        $this->model->savePost($id, [
            'title'      => 'Post to Delete',
            'slug'       => $slug,
            'content'    => 'Will be deleted',
            'created_at' => time(),
            'status'     => 'published',
        ]);

        $this->assertNotNull($this->model->find($id));

        $deleted = $this->model->deletePost($id);
        $this->assertTrue($deleted);
        $this->assertNull($this->model->find($id));
        $this->assertNull($this->model->findBySlug($slug));
    }

    public function testAllComplianceWithCoreFreeze(): void
    {
        $id = $this->model->nextId();
        $this->createdIds[] = $id;

        $this->model->savePost($id, [
            'title'      => 'Active Post ' . $id,
            'slug'       => 'active-post-' . $id,
            'content'    => 'Active content',
            'created_at' => time(),
            'status'     => 'published',
        ]);

        $allPosts = $this->model->all();
        $this->assertIsArray($allPosts);
        $this->assertArrayHasKey((string) $id, $allPosts);
    }
}
