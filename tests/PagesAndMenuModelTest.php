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

    public function testMenuReorderingMoveUpAndMoveDown(): void
    {
        // Save initial state to restore later
        $originalItems = $this->menuModel->getItems();

        // Create 3 predictable items
        $this->menuModel->save([
            ['label' => 'Item A', 'url' => '/a', 'order' => 0],
            ['label' => 'Item B', 'url' => '/b', 'order' => 1],
            ['label' => 'Item C', 'url' => '/c', 'order' => 2],
        ]);

        $items = $this->menuModel->getItems();
        $this->assertCount(3, $items);
        $this->assertSame('Item A', $items[0]['label']);
        $this->assertSame('Item B', $items[1]['label']);
        $this->assertSame('Item C', $items[2]['label']);

        // Move Item B (index 1) UP -> should now be at index 0
        $movedUp = $this->menuModel->moveUp(1);
        $this->assertTrue($movedUp);

        $itemsAfterUp = $this->menuModel->getItems();
        $this->assertSame('Item B', $itemsAfterUp[0]['label']);
        $this->assertSame('Item A', $itemsAfterUp[1]['label']);
        $this->assertSame('Item C', $itemsAfterUp[2]['label']);

        // Try moving index 0 UP -> should fail (cannot move above top)
        $this->assertFalse($this->menuModel->moveUp(0));

        // Move Item B (now at index 0) DOWN -> should now be back at index 1
        $movedDown = $this->menuModel->moveDown(0);
        $this->assertTrue($movedDown);

        $itemsAfterDown = $this->menuModel->getItems();
        $this->assertSame('Item A', $itemsAfterDown[0]['label']);
        $this->assertSame('Item B', $itemsAfterDown[1]['label']);
        $this->assertSame('Item C', $itemsAfterDown[2]['label']);

        // Try moving index 2 DOWN -> should fail (cannot move below bottom)
        $this->assertFalse($this->menuModel->moveDown(2));

        // Restore original items
        $this->menuModel->save($originalItems);
    }
}
