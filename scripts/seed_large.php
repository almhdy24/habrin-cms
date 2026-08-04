<?php

declare(strict_types=1);

/**
 * Pico JSON CMS — Large Dataset Seeder
 *
 * Generates a large JSON object without loading all posts
 * into PHP memory at once.
 *
 * Usage:
 *   php scripts/seed_large.php
 *   php scripts/seed_large.php 500000
 *   php scripts/seed_large.php 500000 10000
 */

ini_set('memory_limit', '512M');

require_once __DIR__ . '/../vendor/autoload.php';

use Core\App;
use Models\PostsModel;

$total = isset($argv[1]) ? max(1, (int) $argv[1]) : 500000;
$progressEvery = isset($argv[2]) ? max(1000, (int) $argv[2]) : 10000;

$postsFile = App::path('content', 'posts.json');
$indexFile = App::path('content', 'posts.index.json');
$tempFile = $postsFile . '.seed.tmp';

echo "========================================\n";
echo " Pico JSON CMS — Large Dataset Seeder\n";
echo "========================================\n";
echo "Total posts: " . number_format($total) . "\n";
echo "Progress: every " . number_format($progressEvery) . " posts\n\n";

$start = microtime(true);

$categories = [
    'Technology', 'PHP', 'Web Dev', 'Tutorials', 'News',
    'Security', 'DevOps', 'Design', 'Backend', 'Frontend',
    'Database', 'Cloud', 'AI', 'Machine Learning', 'IoT'
];

$authors = ['Elmahdi', 'Admin', 'Guest', 'Editor', 'Contributor', 'Reviewer'];

$paragraphs = [
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
    'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
    'This is a performance test post for Pico JSON CMS.',
    'The purpose of this post is to benchmark the JSON storage system.',
    'Pico JSON CMS uses flat-file JSON storage.',
    'This dataset is generated for performance testing.'
];

$baseTime = time() - ($total * 2);

$fp = fopen($tempFile, 'wb');
if ($fp === false) {
    fwrite(STDERR, "ERROR: Cannot open temporary file.\n");
    exit(1);
}

try {
    fwrite($fp, "{\n");
    $first = true;

    for ($j = 1; $j <= $total; $j++) {
        $id = $baseTime + $j;
        $slug = 'post-' . $j;
        $category = $categories[$j % count($categories)];
        $author = $authors[$j % count($authors)];

        $numParas = 4 + ($j % 3);
        $contentParts = [];
        for ($p = 0; $p < $numParas; $p++) {
            $contentParts[] = $paragraphs[($j + $p) % count($paragraphs)];
        }
        $content = implode("\n\n", $contentParts);

        $post = [
            'title' => "Post #{$j}: {$category}",
            'slug' => $slug,
            'content' => $content,
            'category' => $category,
            'author' => $author,
            'created_at' => $baseTime + $j,
            'updated_at' => $baseTime + $j,
            'deleted_at' => null,
        ];

        $json = json_encode((string) $id, JSON_UNESCAPED_UNICODE);
        $postJson = json_encode($post, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false || $postJson === false) {
            throw new RuntimeException("JSON encoding failed at post {$j}");
        }

        if (!$first) {
            fwrite($fp, ",\n");
        }
        fwrite($fp, '  ' . $json . ': ' . $postJson);
        $first = false;

        if ($j % $progressEvery === 0 || $j === $total) {
            $elapsed = microtime(true) - $start;
            $rate = $elapsed > 0 ? $j / $elapsed : 0;
            $percent = ($j / $total) * 100;
            echo sprintf(
                "\rProgress: %s / %s (%.1f%%) | %.0f posts/sec",
                number_format($j),
                number_format($total),
                $percent,
                $rate
            );
            fflush(STDOUT);
        }
    }

    fwrite($fp, "\n}\n");
    fflush($fp);
    fclose($fp);

    echo "\n\n";

    // Atomic replacement
    if (!rename($tempFile, $postsFile)) {
        throw new RuntimeException('Failed to replace posts.json');
    }

    echo "Building slug index...\n";

    $model = new PostsModel();
    $model->clearCache();
    $model->rebuildSlugIndex();
    $model->clearCache();

    $elapsed = microtime(true) - $start;

    clearstatcache(true, $postsFile);
    clearstatcache(true, $indexFile);

    $size = file_exists($postsFile) ? filesize($postsFile) : 0;
    $indexSize = file_exists($indexFile) ? filesize($indexFile) : 0;

    echo "\n========================================\n";
    echo "Seeder completed successfully\n";
    echo "========================================\n";
    echo "Total posts:   " . number_format($total) . "\n";
    echo "posts.json:    " . number_format($size / 1024 / 1024, 2) . " MB\n";
    echo "index.json:    " . number_format($indexSize / 1024 / 1024, 2) . " MB\n";
    echo "Time elapsed:  " . number_format($elapsed, 2) . " sec\n";
    echo "========================================\n";

} catch (Throwable $e) {
    if (is_resource($fp)) fclose($fp);
    @unlink($tempFile);
    fwrite(STDERR, "\nERROR: " . $e->getMessage() . "\n");
    exit(1);
}
