<?php
/**
 * Performance Seeder – Bulk Mode
 * Usage: php scripts/seed.php [count]
 * Default: 1000
 */

declare(strict_types=1);

ini_set('memory_limit', '512M');

require_once __DIR__ . '/../vendor/autoload.php';

use Core\App;
use Models\PostsModel;

$count = isset($argv[1]) ? max(1, (int) $argv[1]) : 1000;

echo "========================================\n";
echo " Habrin CMS — Bulk Seeder\n";
echo "========================================\n";
echo "Generating {$count} posts...\n\n";

$start = microtime(true);
$memoryStart = memory_get_usage();

$model = new PostsModel();

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
    'This is a performance test post for Habrin CMS. The purpose is to benchmark the JSON storage system.',
    'The system should handle thousands of posts efficiently with atomic writes and slug indexing.',
    'Habrin CMS uses flat-file JSON storage, making it ideal for small to medium websites.',
    'With proper indexing, lookups remain fast even with large datasets.'
];

$posts = [];
$baseTime = time() - ($count * 5);

for ($i = 1; $i <= $count; $i++) {
    $id = $baseTime + $i;
    $slug = 'post-' . $i;

    // Build content with 4-6 paragraphs
    $numParas = 4 + ($i % 3);
    $contentParts = [];
    for ($p = 0; $p < $numParas; $p++) {
        $contentParts[] = $paragraphs[($i + $p) % count($paragraphs)];
    }
    $content = implode("\n\n", $contentParts);

    $posts[(string) $id] = [
        'title' => "Post #{$i}: " . $categories[$i % count($categories)],
        'slug' => $slug,
        'content' => $content,
        'category' => $categories[$i % count($categories)],
        'author' => $authors[$i % count($authors)],
        'created_at' => $baseTime + $i,
        'updated_at' => $baseTime + $i,
        'deleted_at' => null,   // explicitly null
    ];

    if ($i % 1000 === 0 || $i === $count) {
        echo "  Generated {$i}/{$count} posts...\n";
    }
}

echo "\nWriting {$count} posts to disk atomically...\n";

$writeStart = microtime(true);
$result = $model->bulkImport($posts);

$writeTime = microtime(true) - $writeStart;
$elapsed = microtime(true) - $start;
$memoryPeak = memory_get_peak_usage();

if ($result) {
    $size = filesize(App::path('content', 'posts.json'));
    $indexSize = filesize(App::path('content', 'posts.index.json'));

    echo "\nSeed completed successfully.\n";
    echo "========================================\n";
    echo "Posts:           " . number_format($count) . "\n";
    echo "posts.json:      " . number_format($size / 1024, 2) . " KB\n";
    echo "posts.index.json:" . number_format($indexSize / 1024, 2) . " KB\n";
    echo "Write time:      " . number_format($writeTime, 3) . " sec\n";
    echo "Total time:      " . number_format($elapsed, 3) . " sec\n";
    echo "Memory peak:     " . number_format($memoryPeak / 1024 / 1024, 2) . " MB\n";
    echo "========================================\n";
} else {
    echo "\nSeed failed.\n";
    exit(1);
}
