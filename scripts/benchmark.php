<?php
/**
 * Performance Benchmark
 * Usage: php scripts/benchmark.php [iterations]
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Models\PostsModel;

$iterations = (int) ($argv[1] ?? 100);

echo "========================================\n";
echo " Pico JSON CMS — Benchmark\n";
echo "========================================\n";

$model = new PostsModel();

// 1. Test findBySlug
echo "\n1. findBySlug() – {$iterations} lookups\n";
$start = microtime(true);
$found = 0;
$miss = 0;
for ($i = 1; $i <= $iterations; $i++) {
    $slug = 'test-post-' . ($i * 17); // random-ish
    if ($model->findBySlug($slug) !== null) {
        $found++;
    } else {
        $miss++;
    }
}
$elapsed = microtime(true) - $start;
echo "   Found:       {$found}/{$iterations}\n";
echo "   Miss:        {$miss}/{$iterations}\n";
echo "   Time:        " . number_format($elapsed, 4) . " sec\n";
echo "   Avg:         " . number_format(($elapsed / $iterations) * 1000, 3) . " ms\n";
echo "   Lookups/sec: " . number_format($iterations / max($elapsed, 0.00001), 0) . "\n";

// 2. Test all() full load
echo "\n2. all() – full load\n";
$start = microtime(true);
$all = $model->all();
$elapsed = microtime(true) - $start;
echo "   Posts loaded: " . number_format(count($all)) . "\n";
echo "   Time:         " . number_format($elapsed, 4) . " sec\n";

// 3. Test published() pagination
echo "\n3. published(1, 5) – first page\n";
$start = microtime(true);
$result = $model->published(1, 5);
$elapsed = microtime(true) - $start;
echo "   Total posts:  " . number_format($result['total']) . "\n";
echo "   Total pages:  " . number_format($result['totalPages']) . "\n";
echo "   Time:         " . number_format($elapsed, 4) . " sec\n";

// 4. Test cache hit
echo "\n4. all() – cached\n";
$start = microtime(true);
$model->all();
$elapsed = microtime(true) - $start;
echo "   Time:         " . number_format($elapsed, 6) . " sec (should be near 0)\n";

echo "\n========================================\n";
