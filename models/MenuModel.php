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
        $items = array_values($this->all());
        usort($items, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        return $items;
    }

    public function addItem(array $item): bool
    {
        $items = $this->getItems();
        if (!isset($item['order']) || $item['order'] === 0) {
            $item['order'] = count($items);
        }
        $items[] = $item;
        return $this->save(array_values($items));
    }

    public function updateItem(int $index, array $item): bool
    {
        $items = $this->getItems();
        if (!isset($items[$index])) {
            return false;
        }
        $items[$index] = $item;
        return $this->save(array_values($items));
    }

    public function deleteItem(int $index): bool
    {
        $items = $this->getItems();
        if (!isset($items[$index])) {
            return false;
        }
        array_splice($items, $index, 1);
        foreach ($items as $i => &$it) {
            $it['order'] = $i;
        }
        unset($it);
        return $this->save(array_values($items));
    }

    public function moveUp(int $index): bool
    {
        $items = $this->getItems();
        if ($index <= 0 || !isset($items[$index])) {
            return false;
        }
        $prev = $items[$index - 1];
        $items[$index - 1] = $items[$index];
        $items[$index] = $prev;

        foreach ($items as $i => &$it) {
            $it['order'] = $i;
        }
        unset($it);

        return $this->save(array_values($items));
    }

    public function moveDown(int $index): bool
    {
        $items = $this->getItems();
        if ($index < 0 || !isset($items[$index]) || !isset($items[$index + 1])) {
            return false;
        }
        $next = $items[$index + 1];
        $items[$index + 1] = $items[$index];
        $items[$index] = $next;

        foreach ($items as $i => &$it) {
            $it['order'] = $i;
        }
        unset($it);

        return $this->save(array_values($items));
    }

    public function reorder(array $order): bool
    {
        // $order is array of indices in new order
        $items = $this->getItems();
        $newItems = [];
        foreach ($order as $idx) {
            if (isset($items[$idx])) {
                $newItems[] = $items[$idx];
            }
        }
        foreach ($newItems as $i => &$it) {
            $it['order'] = $i;
        }
        unset($it);
        return $this->save($newItems);
    }
}
