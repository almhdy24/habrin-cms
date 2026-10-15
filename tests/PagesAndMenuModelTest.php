<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Models\PagesModel;
use Models\MenuModel;

final class PagesAndMenuModelTest extends TestCase
{
    private PagesModel $pagesModel;
    private MenuModel $menuModel;
    private array $createdPageSlugs = [];

    protected function setUp(): void
    {
        $this->pagesModel = new PagesModel();
        $this->menuModel = new MenuModel();
        $this->createdPageSlugs = [];
    }

    protected function tearDown(): void
    {
        foreach ($this->createdPageSlugs as $slug) {
            $this->pagesModel->deletePage($slug);
        }
    }

    public function testPageCrudWorkflow(): void
    {
        $slug = 'test-static-page-' . uniqid();
        $this->createdPageSlugs[] = $slug;

        $pageData = [
            'title'      => 'Test Static Page',
            'content'    => '# Hello World\nThis is static content.',
            'created_at' => time(),
            'updated_at' => time(),
        ];

        // Save page
        $saved = $this->pagesModel->savePage($slug, $pageData);
        $this->assertTrue($saved);

        // Find by slug
        $found = $this->pagesModel->findBySlug($slug);
        $this->assertNotNull($found);
        $this->assertSame('Test Static Page', $found['title']);

        // Check getAll contains the slug
        $all = $this->pagesModel->getAll();
        $this->assertArrayHasKey($slug, $all);

        // Delete page
        $deleted = $this->pagesModel->deletePage($slug);
        $this->assertTrue($deleted);
        $this->assertNull($this->pagesModel->findBySlug($slug));
    }

    public function testMenuCrudWorkflow(): void
    {
        $initialCount = count($this->menuModel->getItems());

        $uniqueLabel = 'Test Link ' . uniqid();
        $saved = $this->menuModel->addItem([
            'label' => $uniqueLabel,
            'url'   => '/test-link',
            'order' => 999,
        ]);
        $this->assertTrue($saved);

        $items = $this->menuModel->getItems();
        $this->assertSame($initialCount + 1, count($items));

        // Find our added item index
        $index = null;
        foreach ($this->menuModel->all() as $i => $item) {
            if (($item['label'] ?? '') === $uniqueLabel) {
                $index = $i;
                break;
            }
        }
        $this->assertNotNull($index);

        // Update item
        $updated = $this->menuModel->updateItem($index, [
            'label' => $uniqueLabel . ' Updated',
            'url'   => '/test-link-updated',
            'order' => 999,
        ]);
        $this->assertTrue($updated);

        $all = $this->menuModel->all();
        $this->assertSame($uniqueLabel . ' Updated', $all[$index]['label']);

        // Delete item
        $deleted = $this->menuModel->deleteItem($index);
        $this->assertTrue($deleted);
        $this->assertSame($initialCount, count($this->menuModel->getItems()));
    }
}
