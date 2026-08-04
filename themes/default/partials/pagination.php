<?php
$basePath = $basePath ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$pageUrl = fn($p) => $this->route($basePath . '?page=' . $p);

function renderPagination($current, $total, $pageUrl) {
    $range = 2;
    $pages = [1];

    if ($current - $range > 2) {
        $pages[] = '...';
    }

    for ($i = max(2, $current - $range); $i <= min($total - 1, $current + $range); $i++) {
        if ($i > 1 && $i < $total) {
            $pages[] = $i;
        }
    }

    if ($current + $range < $total - 1) {
        $pages[] = '...';
    }

    if ($total > 1) {
        $pages[] = $total;
    }

    $pages = array_unique($pages);

    foreach ($pages as $p) {
        if ($p === '...') {
            echo '<span class="pagination-ellipsis">…</span>';
        } elseif ($p == $current) {
            echo '<span class="pagination-link is-current">' . $p . '</span>';
        } else {
            echo '<a href="' . $pageUrl($p) . '" class="pagination-link">' . $p . '</a>';
        }
    }
}
?>

<?php if ($totalPages > 1): ?>
<div class="pagination" style="margin-top: 1.5rem;">
  <?php if ($currentPage > 1): ?>
    <a href="<?= $pageUrl($currentPage - 1) ?>" class="pagination-link" aria-label="Previous">
      <i data-lucide="chevron-left"></i>
    </a>
  <?php endif; ?>

  <?php renderPagination($currentPage, $totalPages, $pageUrl); ?>

  <?php if ($currentPage < $totalPages): ?>
    <a href="<?= $pageUrl($currentPage + 1) ?>" class="pagination-link" aria-label="Next">
      <i data-lucide="chevron-right"></i>
    </a>
  <?php endif; ?>
</div>
<?php endif; ?>
