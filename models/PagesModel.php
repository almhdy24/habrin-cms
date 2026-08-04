<?php
/**
 * Static Pages Model
 * Stores pages in content/pages.json
 * Structure: { "slug": { "title", "content", "created_at", "updated_at" } }
 */

declare(strict_types=1);

namespace Models;

use Core\App;
use Core\Model;

class PagesModel extends Model
{
    protected function boot(): void
    {
        $this->file = App::path('content', 'pages.json');
    }

    public function findBySlug(string $slug): ?array
    {
        $all = $this->all();
        return $all[$slug] ?? null;
    }

    public function savePage(string $slug, array $data): bool
    {
        $pages = $this->all();
        $pages[$slug] = $data;
        return $this->save($pages);
    }

    public function deletePage(string $slug): bool
    {
        $pages = $this->all();
        if (!isset($pages[$slug])) {
            return false;
        }
        unset($pages[$slug]);
        return $this->save($pages);
    }

    public function getAll(): array
    {
        return $this->all();
    }
}
