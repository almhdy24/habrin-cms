<?php
/**
 * Menu Model
 * Stores menu items in content/menu.json
 * Structure: [ { "label": "Home", "url": "/", "order": 0 }, ... ]
 */

declare(strict_types=1);

namespace Models;

use Core\App;
use Core\Model;

class MenuModel extends Model
{
    protected function boot(): void
    {
        $this->file = App::path('content', 'menu.json');
    }

    public function getItems(): array
    {
        $items = $this->all();
        usort($items, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        return $items;
    }

    public function addItem(array $item): bool
    {
        $items = $this->all();
        $items[] = $item;
        return $this->save($items);
    }

    public function updateItem(int $index, array $item): bool
    {
        $items = $this->all();
        if (!isset($items[$index])) {
            return false;
        }
        $items[$index] = $item;
        return $this->save($items);
    }

    public function deleteItem(int $index): bool
    {
        $items = $this->all();
        if (!isset($items[$index])) {
            return false;
        }
        array_splice($items, $index, 1);
        return $this->save($items);
    }

    public function reorder(array $order): bool
    {
        // $order is array of indices in new order
        $items = $this->all();
        $newItems = [];
        foreach ($order as $idx) {
            if (isset($items[$idx])) {
                $newItems[] = $items[$idx];
            }
        }
        return $this->save($newItems);
    }
}
