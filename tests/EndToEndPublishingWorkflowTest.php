<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Core\App;
use Core\View;
use Models\PostsModel;
use Models\MediaModel;

final class EndToEndPublishingWorkflowTest extends TestCase
{
    private PostsModel $posts;
    private array $createdPostIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        App::loadConfig();
        View::clearCache();

        $md = App::path('plugins', 'markdown', 'plugin.php');
        if (file_exists($md)) {
            require_once $md;
        }

        $this->posts = new PostsModel();
    }

    protected function tearDown(): void
    {
        foreach ($this->createdPostIds as $id) {
            $this->posts->deletePost($id);
        }
        $this->posts->syncMetadataAndIndexes();
        parent::tearDown();
    }

    public function testEndToEndPostLifecycle(): void
    {
        // 1. Create a published post with Markdown and image
        $title = 'E2E Publishing Test ' . uniqid();
        $content = "---\nfeatured: true\n---\n# Master Headline\n\nThis is an **end-to-end** article.\n\n![Diagram](/assets/uploads/test.png)\n\n| Item | Qty |\n| --- | --- |\n| Test | 100 |";
        
        $postId = $this->posts->nextId();
        $slug = $this->posts->generateSlug($title);

        $saved = $this->posts->savePost($postId, [
            'id' => $postId,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'status' => 'published',
            'created_at' => time(),
            'updated_at' => time(),
            'deleted_at' => null,
        ]);

        $this->assertTrue($saved);
        $this->createdPostIds[] = $postId;

        // 2. Fetch the created post
        $post = $this->posts->find($postId);
        $this->assertNotNull($post);
        $this->assertEquals($title, $post['title']);
        $this->assertEquals('published', $post['status']);

        // 3. Verify slug lookup works
        $slug = $post['slug'];
        $this->assertNotEmpty($slug);
        $postBySlug = $this->posts->findBySlug($slug);
        $this->assertNotNull($postBySlug);
        $this->assertEquals($postId, $postBySlug['id']);

        // 4. Verify post appears in published posts on homepage
        $published = $this->posts->published(1, 10);
        $this->assertArrayHasKey($postId, $published['items']);

        // 5. Verify rendered output contains converted Markdown HTML
        $rendered = apply_filters('post_content', $post['content'], $post);
        $this->assertStringContainsString('<h1>Master Headline</h1>', $rendered);
        $this->assertStringContainsString('<strong>end-to-end</strong>', $rendered);
        $this->assertStringContainsString('<img src="/assets/uploads/test.png"', $rendered);
        $this->assertStringContainsString('<table>', $rendered);
        // Ensure front matter is stripped
        $this->assertStringNotContainsString('featured: true', $rendered);

        // 6. Update the post to draft
        $updated = $this->posts->savePost($postId, array_merge($post, [
            'title' => $title . ' (Draft)',
            'content' => '# Updated Content',
            'status' => 'draft',
            'updated_at' => time(),
        ]));
        $this->assertTrue($updated);
        $post = $this->posts->find($postId);

        // 7. Verify draft does NOT appear in published list
        $publishedAfterDraft = $this->posts->published(1, 10);
        $this->assertArrayNotHasKey($postId, $publishedAfterDraft['items']);

        // 8. Soft-delete (move to trash)
        $post['deleted_at'] = time();
        $this->posts->savePost($postId, $post);

        $trashList = $this->posts->trashed();
        $this->assertArrayHasKey((string) $postId, $trashList);

        // 9. Restore from trash
        $post['deleted_at'] = null;
        $this->posts->savePost($postId, $post);

        $postRestored = $this->posts->find($postId);
        $this->assertNotNull($postRestored);
        $this->assertNull($postRestored['deleted_at']);
        $this->assertEquals('draft', $postRestored['status']);
    }

    public function testMediaUploadValidationAndDeletion(): void
    {
        $media = new MediaModel();

        // Test non-file rejection
        $invalidUpload = [
            'name' => 'malicious.php',
            'type' => 'application/x-php',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0,
        ];
        $result = $media->upload($invalidUpload);
        $this->assertFalse($result['success']);

        // Test directory traversal prevention
        $this->assertFalse($media->delete('../../config.php'));
    }

    public function testCustomSlugExcerptAndTagsWorkflow(): void
    {
        $postId = $this->posts->nextId();
        $this->createdPostIds[] = $postId;

        $title = 'Article with Custom Metadata ' . uniqid();
        $customSlug = 'custom-vanity-url-' . uniqid();
        $excerpt = 'This is a custom SEO excerpt summarizing the entire article.';
        $tags = ['engineering', 'php8', 'cms'];

        // Save post with custom slug, excerpt, and tags
        $saved = $this->posts->savePost($postId, [
            'id' => $postId,
            'title' => $title,
            'slug' => $customSlug,
            'excerpt' => $excerpt,
            'tags' => $tags,
            'content' => 'Main content body here.',
            'status' => 'published',
            'created_at' => time(),
            'updated_at' => time(),
            'deleted_at' => null,
        ]);
        $this->assertTrue($saved);

        // Fetch and assert
        $post = $this->posts->find($postId);
        $this->assertNotNull($post);
        $this->assertSame($customSlug, $post['slug']);
        $this->assertSame($excerpt, $post['excerpt']);
        $this->assertSame($tags, $post['tags']);

        // Verify lookup by custom slug
        $postBySlug = $this->posts->findBySlug($customSlug);
        $this->assertNotNull($postBySlug);
        $this->assertSame($postId, $postBySlug['id']);

        // Update post without changing slug, verify slug is preserved
        $post['title'] = 'Updated Title Without Changing Slug';
        $post['updated_at'] = time();
        $this->posts->savePost($postId, $post);

        $postAfterUpdate = $this->posts->find($postId);
        $this->assertSame($customSlug, $postAfterUpdate['slug']);
        $this->assertSame('Updated Title Without Changing Slug', $postAfterUpdate['title']);
    }
}
