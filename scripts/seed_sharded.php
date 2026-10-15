<?php
/**
 * Sharded Seeder – memory-safe, chunked, with final index rebuild
 * Usage: php scripts/seed_sharded.php [total] [chunk_size]
 * Default: 500000, 10000
 */

ini_set('memory_limit', '512M');
require_once __DIR__ . '/../vendor/autoload.php';

use Models\PostsModel;

$total = isset($argv[1]) ? max(1, (int) $argv[1]) : 500000;
$chunkSize = isset($argv[2]) ? max(100, (int) $argv[2]) : 10000;

echo "========================================\n";
echo " Sharded Seeder (JSON-only, memory-safe)\n";
echo "========================================\n";
echo "Total: " . number_format($total) . "\n";
echo "Chunk size: " . number_format($chunkSize) . "\n\n";

$start = microtime(true);
$model = new PostsModel();

$categories = ['Technology','PHP','Web Dev','Tutorials','News','Security','DevOps','Design'];
$authors = ['Elmahdi','Admin','Guest','Editor','Contributor'];
$paragraphs = [
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'Ut enim ad minim veniam, quis nostrud exercitation ullamco.',
    'Duis aute irure dolor in reprehenderit in voluptate.',
    'Excepteur sint occaecat cupidatat non proident.',
    'This is a performance test post for Habrin CMS.'
];

$baseTime = time();

$totalChunks = ceil($total / $chunkSize);
$lastId = 0;

for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
    $startId = $chunk * $chunkSize + 1;
    $endId = min(($chunk + 1) * $chunkSize, $total);
    $chunkCount = $endId - $startId + 1;

    echo sprintf("\rChunk %d/%d: %d posts...", $chunk + 1, $totalChunks, $chunkCount);

    $posts = [];
    for ($j = $startId; $j <= $endId; $j++) {
        $post = [
            'title' => "Post #{$j}: " . $categories[$j % count($categories)],
            'slug' => 'post-' . $j,
            'content' => $paragraphs[$j % count($paragraphs)] . "\n\n" . $paragraphs[($j+1) % count($paragraphs)],
            'category' => $categories[$j % count($categories)],
            'author' => $authors[$j % count($authors)],
            'created_at' => $baseTime + $j,
            'updated_at' => $baseTime + $j,
            'deleted_at' => null,
        ];
        $posts[(string) $j] = $post;
    }

    $model->bulkImportChunk($posts);
    unset($posts); // free memory
}

echo "\n";

echo "Finalizing page index (one time, may take a few seconds)...\n";
$model->finalizeIndex();

$elapsed = microtime(true) - $start;
$published = $model->publishedCount();

echo "\n✅ Done in " . number_format($elapsed, 2) . " sec\n";
echo "Total posts: " . number_format($total) . "\n";
echo "Published: " . number_format($published) . "\n";
