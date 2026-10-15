<?php

declare(strict_types=1);

namespace Models;

use Core\App;

/**
 * Sharded JSON Storage – full incremental index support
 * - Page index is maintained incrementally on save/delete
 * - Slug index is hashed and incremental
 * - No full rebuild on individual operations
 */
class PostsModel
{
    private string $postDir;
    private string $indexDir;
    private string $metadataFile;
    private string $pageIndexDir;
    private string $slugIndexDir;

    private int $shardSize = 5000;
    private int $pageChunkSize = 1000;
    private int $slugBuckets = 256;

    private array $metadata = [];

    // --------------------------------------------------------------------
    // Constructor
    // --------------------------------------------------------------------

    public function __construct()
    {
        $this->postDir = App::path('content', 'posts');
        $this->indexDir = App::path('content', 'indexes');
        $this->pageIndexDir = $this->indexDir . '/pages';
        $this->slugIndexDir = $this->indexDir . '/slugs';
        $this->metadataFile = $this->indexDir . '/metadata.json';

        $this->ensureDirectories();
        $this->loadMetadata();
    }

    private function ensureDirectories(): void
    {
        $dirs = [$this->postDir, $this->indexDir, $this->pageIndexDir, $this->slugIndexDir];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0755, true);
        }
    }

    // --------------------------------------------------------------------
    // Metadata
    // --------------------------------------------------------------------

    private function loadMetadata(): void
    {
        if (file_exists($this->metadataFile)) {
            $data = json_decode(file_get_contents($this->metadataFile), true);
            if (is_array($data)) {
                $this->metadata = $data;
                return;
            }
        }
        $this->metadata = [
            'total' => 0,
            'published' => 0,
            'drafts' => 0,
            'trashed' => 0,
            'shards' => 0,
            'last_id' => 0,
            'shard_size' => $this->shardSize,
            'page_chunk_size' => $this->pageChunkSize,
            'slug_buckets' => $this->slugBuckets,
        ];
    }

    private function saveMetadata(): void
    {
        file_put_contents(
            $this->metadataFile,
            json_encode($this->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    public function publishedCount(): int
    {
        return $this->metadata['published'] ?? 0;
    }

    public function trashedCount(): int
    {
        return $this->metadata['trashed'] ?? 0;
    }

    public function draftsCount(): int
    {
        return $this->metadata['drafts'] ?? 0;
    }

    public function isTrashed(array $post): bool
    {
        $val = $post['deleted_at'] ?? null;
        return !($val === null || $val === 0 || $val === '' || $val === 'null' || $val === 'NULL' || $val === false);
    }

    public function isDraft(array $post): bool
    {
        return !$this->isTrashed($post) && ($post['status'] ?? 'published') === 'draft';
    }

    public function isPublished(array $post): bool
    {
        return !$this->isTrashed($post) && ($post['status'] ?? 'published') === 'published';
    }

    /**
     * Get the next available ID (atomic)
     */
    public function nextId(): int
    {
        $this->loadMetadata(); // refresh
        $id = ($this->metadata['last_id'] ?? 0) + 1;
        $this->metadata['last_id'] = $id;
        $this->saveMetadata();
        return $id;
    }

    // --------------------------------------------------------------------
    // Helpers
    // --------------------------------------------------------------------

    private function getShardFile(int $shard): string
    {
        return $this->postDir . '/shard-' . sprintf('%06d', $shard) . '.json';
    }

    private function getPageIndexFile(int $chunk): string
    {
        return $this->pageIndexDir . '/page-' . sprintf('%06d', $chunk) . '.json';
    }

    private function getSlugBucketFile(int $bucket): string
    {
        return $this->slugIndexDir . '/slug-' . sprintf('%03d', $bucket) . '.json';
    }

    private function getShardForId(int $id): int
    {
        if ($id < 1) throw new \InvalidArgumentException('ID must be > 0');
        return intdiv($id - 1, $this->shardSize);
    }

    private function getSlugBucket(string $slug): int
    {
        return crc32($slug) % $this->slugBuckets;
    }

    // --------------------------------------------------------------------
    // Read Methods
    // --------------------------------------------------------------------

    private function readShard(int $shard): array
    {
        $file = $this->getShardFile($shard);
        if (!file_exists($file)) return [];
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    private function writeShard(int $shard, array $data): bool
    {
        $file = $this->getShardFile($shard);
        $temp = $file . '.tmp';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) return false;
        if (file_put_contents($temp, $json, LOCK_EX) === false) return false;
        return rename($temp, $file);
    }

    private function readPageChunk(int $chunk): array
    {
        $file = $this->getPageIndexFile($chunk);
        if (!file_exists($file)) return [];
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    private function writePageChunk(int $chunk, array $entries): bool
    {
        $file = $this->getPageIndexFile($chunk);
        $temp = $file . '.tmp';
        $json = json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) return false;
        if (file_put_contents($temp, $json, LOCK_EX) === false) return false;
        return rename($temp, $file);
    }

    private function readSlugBucket(int $bucket): array
    {
        $file = $this->getSlugBucketFile($bucket);
        if (!file_exists($file)) return [];
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    private function writeSlugBucket(int $bucket, array $data): bool
    {
        $file = $this->getSlugBucketFile($bucket);
        $temp = $file . '.tmp';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) return false;
        if (file_put_contents($temp, $json, LOCK_EX) === false) return false;
        return rename($temp, $file);
    }

    // --------------------------------------------------------------------
    // Incremental Page Index
    // --------------------------------------------------------------------

    /**
     * Insert a published post into the page index (sorted by created_at DESC)
     */
    private function insertIntoPageIndex(int $id, int $shard, int $createdAt): void
    {
        $entry = [$id, $shard, $createdAt];

        // Find which chunk should contain this entry based on created_at order.
        // Since we maintain sorted order, we need to find the correct position.
        // We'll scan chunks from the beginning until we find the right one.
        $chunkIndex = 0;
        $found = false;
        while (true) {
            $entries = $this->readPageChunk($chunkIndex);
            if (empty($entries)) {
                // If this chunk is empty, append here
                $entries[] = [$id, $shard];
                $this->writePageChunk($chunkIndex, $entries);
                $found = true;
                break;
            }

            // Check if this entry belongs here (newer than the first entry, or older than the last)
            $firstCreated = $entries[0][2] ?? 0;
            $lastCreated = $entries[count($entries) - 1][2] ?? 0;

            if ($createdAt >= $firstCreated) {
                // Insert at the beginning of this chunk (or after entries with same created_at)
                $pos = 0;
                while ($pos < count($entries) && $entries[$pos][2] > $createdAt) $pos++;
                array_splice($entries, $pos, 0, [[$id, $shard]]);
                $this->writePageChunk($chunkIndex, $entries);
                $found = true;
                break;
            } elseif ($createdAt <= $lastCreated) {
                // It might be in a later chunk
                $chunkIndex++;
                continue;
            } else {
                // It belongs somewhere inside this chunk
                $pos = 0;
                while ($pos < count($entries) && $entries[$pos][2] > $createdAt) $pos++;
                array_splice($entries, $pos, 0, [[$id, $shard]]);
                $this->writePageChunk($chunkIndex, $entries);
                $found = true;
                break;
            }
        }

        // If we reached the end without finding a place, append to a new chunk
        if (!$found) {
            $entries = [[$id, $shard]];
            $this->writePageChunk($chunkIndex, $entries);
        }

        // Rebalance chunks if any chunk exceeds pageChunkSize
        $this->rebalancePageIndex();
    }

    /**
     * Remove a post from the page index
     */
    private function removeFromPageIndex(int $id): void
    {
        $chunkIndex = 0;
        while (true) {
            $entries = $this->readPageChunk($chunkIndex);
            if (empty($entries)) break;

            $found = false;
            for ($i = 0; $i < count($entries); $i++) {
                if ($entries[$i][0] === $id) {
                    array_splice($entries, $i, 1);
                    $this->writePageChunk($chunkIndex, $entries);
                    $found = true;
                    break;
                }
            }
            if ($found) break;
            $chunkIndex++;
        }

        // Rebalance after removal
        $this->rebalancePageIndex();
    }

    /**
     * Rebalance page index chunks to keep them under pageChunkSize
     */
    private function rebalancePageIndex(): void
    {
        // Collect all entries from all chunks
        $allEntries = [];
        $chunkIndex = 0;
        while (true) {
            $entries = $this->readPageChunk($chunkIndex);
            if (empty($entries)) break;
            $allEntries = array_merge($allEntries, $entries);
            $chunkIndex++;
        }

        if (empty($allEntries)) {
            // Clear all chunk files
            $chunkIndex = 0;
            while (file_exists($this->getPageIndexFile($chunkIndex))) {
                unlink($this->getPageIndexFile($chunkIndex));
                $chunkIndex++;
            }
            return;
        }

        // Sort by created_at DESC (third element)
        usort($allEntries, fn($a, $b) => $b[2] <=> $a[2]);

        // Redistribute into chunks
        $chunks = array_chunk($allEntries, $this->pageChunkSize);
        foreach ($chunks as $idx => $chunk) {
            // Keep only (id, shard) pairs
            $data = array_map(fn($e) => [$e[0], $e[1]], $chunk);
            $this->writePageChunk($idx, $data);
        }

        // Remove extra chunk files
        $total = count($chunks);
        $file = $this->getPageIndexFile($total);
        while (file_exists($file)) {
            unlink($file);
            $total++;
            $file = $this->getPageIndexFile($total);
        }
    }

    // --------------------------------------------------------------------
    // Public API
    // --------------------------------------------------------------------

    public function clearCache(): void {}

    /**
     * Paginated published posts
     */
    public function publishedPaginated(int $page = 1, int $perPage = 5): array
    {
        $total = $this->metadata['published'] ?? 0;
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $chunk = intdiv($offset, $this->pageChunkSize);
        $entries = $this->readPageChunk($chunk);
        $localOffset = $offset % $this->pageChunkSize;

        $items = [];
        $slice = array_slice($entries, $localOffset, $perPage);
        foreach ($slice as $entry) {
            list($id, $shard) = $entry;
            $shardData = $this->readShard($shard);
            if (isset($shardData[(string) $id]) && $this->isPublished($shardData[(string) $id])) {
                $items[(string) $id] = $shardData[(string) $id];
            }
        }

        return [
            'items' => $items,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ];
    }

    /**
     * Alias for publishedPaginated – backward compatibility
     */
    public function published(int $page = 1, int $perPage = 5): array
    {
        return $this->publishedPaginated($page, $perPage);
    }

    /**
     * Find post by slug (hashed bucket)
     */
    public function findBySlug(string $slug, bool $includeDrafts = false): ?array
    {
        $bucket = $this->getSlugBucket($slug);
        $bucketData = $this->readSlugBucket($bucket);
        if (!isset($bucketData[$slug])) return null;

        $entry = $bucketData[$slug];
        $shard = $entry['shard'];
        $id = $entry['id'];
        $shardData = $this->readShard($shard);
        if (isset($shardData[$id])) {
            $post = $shardData[$id];
            if ($this->isPublished($post) || ($includeDrafts && $this->isDraft($post))) {
                return $post + ['id' => (int) $id];
            }
        }
        return null;
    }

    /**
     * Find by numeric ID
     */
    public function find(string|int $id): mixed
    {
        $id = (int) $id;
        $shard = $this->getShardForId($id);
        $shardData = $this->readShard($shard);
        return $shardData[(string) $id] ?? null;
    }

    /**
     * Get all trashed posts (admin trash list)
     */
    public function trashed(): array
    {
        $result = [];
        for ($s = 0; $s < $this->metadata['shards']; $s++) {
            $shardData = $this->readShard($s);
            foreach ($shardData as $id => $post) {
                if ($this->isTrashed($post)) {
                    $result[(string) $id] = $post;
                }
            }
        }
        return $result;
    }

    /**
     * Get all drafts
     */
    public function drafts(): array
    {
        $result = [];
        for ($s = 0; $s < $this->metadata['shards']; $s++) {
            $shardData = $this->readShard($s);
            foreach ($shardData as $id => $post) {
                if ($this->isDraft($post)) {
                    $result[(string) $id] = $post;
                }
            }
        }
        return $result;
    }

    /**
     * Search posts by title, slug, or content
     */
    public function search(string $query, int $page = 1, int $perPage = 10, bool $publishedOnly = true): array
    {
        $q = mb_strtolower(trim($query), 'UTF-8');
        if ($q === '') {
            return $this->published($page, $perPage);
        }

        $matches = [];
        for ($s = 0; $s < $this->metadata['shards']; $s++) {
            $shardData = $this->readShard($s);
            foreach ($shardData as $id => $post) {
                if ($this->isTrashed($post)) {
                    continue;
                }
                if ($publishedOnly && !$this->isPublished($post)) {
                    continue;
                }

                $title = mb_strtolower((string) ($post['title'] ?? ''), 'UTF-8');
                $content = mb_strtolower((string) ($post['content'] ?? ''), 'UTF-8');
                $slug = mb_strtolower((string) ($post['slug'] ?? ''), 'UTF-8');

                if (str_contains($title, $q) || str_contains($slug, $q) || str_contains($content, $q)) {
                    $matches[] = $post + ['id' => (int) $id];
                }
            }
        }

        usort($matches, fn($a, $b) => ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0));

        $total = count($matches);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        return [
            'items' => array_slice($matches, $offset, $perPage),
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];
    }

    /**
     * Generate a unique slug
     */
    public function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = strtolower(
            trim(
                preg_replace('/[^a-z0-9]+/i', '-', $title),
                '-'
            )
        );
        if ($base === '') $base = 'post';

        $slug = $base;
        $i = 2;

        while (true) {
            $existing = $this->findBySlug($slug);
            if ($existing === null) {
                return $slug;
            }
            if ($ignoreId !== null && (int) ($existing['id'] ?? 0) === $ignoreId) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }

    /**
     * Save (create or update) a single post
     */
    public function savePost(int|string $id, array $post): bool
    {
        $id = (int) $id;
        $shard = $this->getShardForId($id);
        $shardData = $this->readShard($shard);
        $oldPost = $shardData[(string) $id] ?? null;
        $oldSlug = $oldPost['slug'] ?? null;
        $wasPublished = $oldPost ? $this->isPublished($oldPost) : false;

        // Write shard
        $shardData[(string) $id] = $post;
        if (!$this->writeShard($shard, $shardData)) return false;

        // Update slug bucket
        $newSlug = $post['slug'] ?? null;
        if ($oldSlug && $oldSlug !== $newSlug) {
            $bucket = $this->getSlugBucket($oldSlug);
            $bucketData = $this->readSlugBucket($bucket);
            unset($bucketData[$oldSlug]);
            $this->writeSlugBucket($bucket, $bucketData);
        }
        if ($newSlug && ($this->isPublished($post) || $this->isDraft($post))) {
            $bucket = $this->getSlugBucket($newSlug);
            $bucketData = $this->readSlugBucket($bucket);
            $bucketData[$newSlug] = ['shard' => $shard, 'id' => (string) $id];
            $this->writeSlugBucket($bucket, $bucketData);
        }

        // Update metadata
        $oldState = $oldPost ? ($this->isTrashed($oldPost) ? 'trashed' : ($this->isDraft($oldPost) ? 'draft' : 'published')) : null;
        $newState = $this->isTrashed($post) ? 'trashed' : ($this->isDraft($post) ? 'draft' : 'published');

        if ($oldState === null) {
            // New post
            $this->metadata['total']++;
            $key = $newState === 'draft' ? 'drafts' : ($newState === 'trashed' ? 'trashed' : 'published');
            $this->metadata[$key] = ($this->metadata[$key] ?? 0) + 1;
            if ($newState === 'published') {
                $this->insertIntoPageIndex($id, $shard, $post['created_at'] ?? time());
            }
            if ($id > ($this->metadata['last_id'] ?? 0)) $this->metadata['last_id'] = $id;
        } elseif ($oldState !== $newState) {
            $oldKey = $oldState === 'draft' ? 'drafts' : ($oldState === 'trashed' ? 'trashed' : 'published');
            $newKey = $newState === 'draft' ? 'drafts' : ($newState === 'trashed' ? 'trashed' : 'published');
            $this->metadata[$oldKey] = max(0, ($this->metadata[$oldKey] ?? 0) - 1);
            $this->metadata[$newKey] = ($this->metadata[$newKey] ?? 0) + 1;

            if ($newState === 'published') {
                $this->insertIntoPageIndex($id, $shard, $post['created_at'] ?? time());
            } elseif ($oldState === 'published') {
                $this->removeFromPageIndex($id);
            }
        }

        if ($shard >= $this->metadata['shards']) {
            $this->metadata['shards'] = $shard + 1;
        }
        $this->saveMetadata();

        return true;
    }

    /**
     * Delete a post permanently
     */
    public function deletePost(int|string $id): bool
    {
        $id = (int) $id;
        $shard = $this->getShardForId($id);
        $shardData = $this->readShard($shard);
        if (!isset($shardData[(string) $id])) return false;

        $post = $shardData[(string) $id];
        $state = $this->isTrashed($post) ? 'trashed' : ($this->isDraft($post) ? 'draft' : 'published');

        unset($shardData[(string) $id]);
        if (!$this->writeShard($shard, $shardData)) return false;

        $slug = $post['slug'] ?? null;
        if ($slug) {
            $bucket = $this->getSlugBucket($slug);
            $bucketData = $this->readSlugBucket($bucket);
            unset($bucketData[$slug]);
            $this->writeSlugBucket($bucket, $bucketData);
        }

        $this->metadata['total'] = max(0, ($this->metadata['total'] ?? 0) - 1);
        $stateKey = $state === 'draft' ? 'drafts' : ($state === 'trashed' ? 'trashed' : 'published');
        $this->metadata[$stateKey] = max(0, ($this->metadata[$stateKey] ?? 0) - 1);
        if ($state === 'published') {
            $this->removeFromPageIndex($id);
        }

        $this->saveMetadata();

        return true;
    }

    // --------------------------------------------------------------------
    // Bulk Import (for seeding) – full rebuild once
    // --------------------------------------------------------------------

    public function bulkImportChunk(array $posts): bool
    {
        $shards = [];
        $slugUpdates = [];
        $pageEntries = [];

        foreach ($posts as $id => $post) {
            $idInt = (int) $id;
            $shard = $this->getShardForId($idInt);
            $shards[$shard][(string) $id] = $post;
            if ($this->isPublished($post)) {
                $pageEntries[] = [$idInt, $shard, $post['created_at'] ?? 0];
                if (!empty($post['slug'])) {
                    $slugUpdates[] = ['slug' => $post['slug'], 'shard' => $shard, 'id' => (string) $idInt];
                }
            }
        }

        foreach ($shards as $shard => $data) {
            $existing = $this->readShard($shard);
            foreach ($data as $id => $post) {
                $existing[$id] = $post;
            }
            if (!$this->writeShard($shard, $existing)) return false;
        }

        foreach ($slugUpdates as $entry) {
            $bucket = $this->getSlugBucket($entry['slug']);
            $bucketData = $this->readSlugBucket($bucket);
            $bucketData[$entry['slug']] = ['shard' => $entry['shard'], 'id' => $entry['id']];
            $this->writeSlugBucket($bucket, $bucketData);
        }

        $totalAdded = count($posts);
        $publishedAdded = count($pageEntries);
        $trashedAdded = $totalAdded - $publishedAdded;
        $maxId = $this->metadata['last_id'] ?? 0;
        foreach ($posts as $id => $post) {
            if ((int) $id > $maxId) $maxId = (int) $id;
        }

        $this->metadata['total'] += $totalAdded;
        $this->metadata['published'] += $publishedAdded;
        $this->metadata['trashed'] += $trashedAdded;
        if ($maxId > $this->metadata['last_id']) $this->metadata['last_id'] = $maxId;

        $maxShard = 0;
        foreach ($shards as $shard => $_) {
            if ($shard > $maxShard) $maxShard = $shard;
        }
        if ($maxShard + 1 > $this->metadata['shards']) {
            $this->metadata['shards'] = $maxShard + 1;
        }
        $this->saveMetadata();

        // For bulk seeding, we rebuild the entire page index once after all chunks.
        // The seeder will call finalizeIndex().
        return true;
    }

    public function finalizeIndex(): void
    {
        $this->rebuildPageIndex();
    }

    /**
     * Full rebuild of page index (used only during finalize or if corrupted)
     */
    private function rebuildPageIndex(): void
    {
        $entries = [];
        for ($s = 0; $s < $this->metadata['shards']; $s++) {
            $shardData = $this->readShard($s);
            foreach ($shardData as $id => $post) {
                if ($this->isPublished($post)) {
                    $created = $post['created_at'] ?? 0;
                    $entries[] = ['id' => (int) $id, 'shard' => $s, 'created' => $created];
                }
            }
        }
        usort($entries, fn($a, $b) => $b['created'] <=> $a['created']);

        $chunks = array_chunk($entries, $this->pageChunkSize);
        foreach ($chunks as $idx => $chunk) {
            $data = array_map(fn($e) => [$e['id'], $e['shard']], $chunk);
            $this->writePageChunk($idx, $data);
        }

        $total = count($chunks);
        $file = $this->getPageIndexFile($total);
        while (file_exists($file)) {
            unlink($file);
            $total++;
            $file = $this->getPageIndexFile($total);
        }
    }

    /**
     * Rebuild slug index (if corrupted)
     */
    public function rebuildSlugIndex(): void
    {
        for ($b = 0; $b < $this->slugBuckets; $b++) {
            $file = $this->getSlugBucketFile($b);
            if (file_exists($file)) unlink($file);
        }

        for ($s = 0; $s < $this->metadata['shards']; $s++) {
            $shardData = $this->readShard($s);
            foreach ($shardData as $id => $post) {
                if ($this->isPublished($post) && !empty($post['slug'])) {
                    $slug = $post['slug'];
                    $bucket = $this->getSlugBucket($slug);
                    $bucketData = $this->readSlugBucket($bucket);
                    $bucketData[$slug] = ['shard' => $s, 'id' => (string) $id];
                    $this->writeSlugBucket($bucket, $bucketData);
                }
            }
        }
    }

    // --------------------------------------------------------------------
    // Base Model Contract Compliance
    // --------------------------------------------------------------------

    /**
     * Get all active (non-trashed) posts
     */
    public function all(): array
    {
        $result = [];
        $shards = (int) ($this->metadata['shards'] ?? 0);
        for ($s = 0; $s < $shards; $s++) {
            $shardData = $this->readShard($s);
            foreach ($shardData as $id => $post) {
                if (!$this->isTrashed($post)) {
                    $result[(string) $id] = $post;
                }
            }
        }
        return $result;
    }
}
