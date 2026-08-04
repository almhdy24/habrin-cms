<?php

declare(strict_types=1);

/**
 * Streaming Seeder – writes posts.json + posts.index.json without loading all posts
 * Usage: php scripts/seed_streaming.php [total] [progress_every]
 * Default: 500000, 10000
 */

ini_set('memory_limit', '512M');

require_once __DIR__ . '/../vendor/autoload.php';

use Core\App;

$total = isset($argv[1]) ? max(1, (int) $argv[1]) : 500000;
$progressEvery = isset($argv[2]) ? max(1000, (int) $argv[2]) : 10000;

$postsFile = App::path('content', 'posts.json');
$indexFile = App::path('content', 'posts.index.json');
$postsTemp = $postsFile . '.seed.tmp';
$indexTemp = $indexFile . '.seed.tmp';

echo "========================================\n";
echo " Streaming Seeder (500k posts, memory-safe)\n";
echo "========================================\n";
echo "Total: " . number_format($total) . "\n\n";

$start = microtime(true);

$categories = ['Technology','PHP','Web Dev','Tutorials','News','Security','DevOps','Design'];
$authors = ['Elmahdi','Admin','Guest','Editor','Contributor'];
$paragraphs = [
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'Ut enim ad minim veniam, quis nostrud exercitation ullamco.',
    'Duis aute irure dolor in reprehenderit in voluptate.',
    'Excepteur sint occaecat cupidatat non proident.',
    'This is a performance test post for Pico JSON CMS.'
];

$baseTime = time() - ($total * 2);

$fpPosts = fopen($postsTemp, 'w');
$fpIndex = fopen($indexTemp, 'w');

if (!$fpPosts || !$fpIndex) {
    throw new RuntimeException('Cannot open temp files.');
}

fwrite($fpPosts, "{\n");
fwrite($fpIndex, "{\n");

$firstPost = true;
$firstIndex = true;

for ($j = 1; $j <= $total; $j++) {
    $id = $baseTime + $j;
    $slug = 'post-' . $j;

    // Build post
    $post = [
        'title' => "Post #{$j}: " . $categories[$j % count($categories)],
        'slug' => $slug,
        'content' => $paragraphs[$j % count($paragraphs)] . "\n\n" . $paragraphs[($j+1) % count($paragraphs)],
        'category' => $categories[$j % count($categories)],
        'author' => $authors[$j % count($authors)],
        'created_at' => $baseTime + $j,
        'updated_at' => $baseTime + $j,
        'deleted_at' => null,
    ];

    $idJson = json_encode((string) $id, JSON_UNESCAPED_UNICODE);
    $postJson = json_encode($post, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Write to posts.json
    if (!$firstPost) fwrite($fpPosts, ",\n");
    fwrite($fpPosts, "  {$idJson}: {$postJson}");
    $firstPost = false;

    // Write to index.json
    $slugJson = json_encode($slug, JSON_UNESCAPED_UNICODE);
    if (!$firstIndex) fwrite($fpIndex, ",\n");
    fwrite($fpIndex, "  {$slugJson}: {$idJson}");
    $firstIndex = false;

    if ($j % $progressEvery === 0 || $j === $total) {
        $elapsed = microtime(true) - $start;
        $rate = $elapsed > 0 ? $j / $elapsed : 0;
        echo sprintf("\rProgress: %s / %s (%.1f%%) | %.0f posts/sec",
            number_format($j), number_format($total), ($j/$total)*100, $rate);
    }
}

fwrite($fpPosts, "\n}\n");
fwrite($fpIndex, "\n}\n");

fclose($fpPosts);
fclose($fpIndex);

rename($postsTemp, $postsFile);
rename($indexTemp, $indexFile);

$elapsed = microtime(true) - $start;
$size = filesize($postsFile);
$idxSize = filesize($indexFile);

echo "\n\n✅ Done!\n";
echo "posts.json: " . number_format($size/1024/1024, 2) . " MB\n";
echo "index.json: " . number_format($idxSize/1024/1024, 2) . " MB\n";
echo "Time: " . number_format($elapsed, 2) . " sec\n";
