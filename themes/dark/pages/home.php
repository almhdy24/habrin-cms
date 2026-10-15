<?php $this->layout('layouts/site', ['title' => ($settings['site_title'] ?? 'Dark') . ' - Feed']) ?>

<section class="tech-feed">
  <?php if (empty($posts)): ?>
    <div class="empty-state">
      <div class="terminal-icon">&gt;_</div>
      <h2>No articles found</h2>
      <p class="text-muted">Repository contains no published logs or articles yet.</p>
      <?php if ($this->is_admin()): ?>
        <p class="mt-3"><a href="/admin/add" class="tech-btn"><i data-lucide="plus"></i> New Entry</a></p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="cards-grid">
      <?php foreach ($posts as $postId => $post): ?>
        <?php
        $wordCount = str_word_count(strip_tags((string) $post['content']));
        $readTime = max(1, (int) ceil($wordCount / 200));
        ?>
        <article class="tech-card">
          <div class="card-meta">
            <span class="mono-date">[<?= date('Y-m-d', (int) $post['created_at']) ?>]</span>
            <span class="badge-read"><?= $readTime ?>m</span>
            <?php if ($this->is_admin()): ?>
              <a href="/admin/edit/<?= (int) $postId ?>" class="card-edit">edit</a>
            <?php endif; ?>
          </div>

          <h2 class="card-title">
            <a href="/post/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a>
          </h2>

          <div class="card-summary">
            <?php
            if (!empty($post['excerpt'])) {
                echo htmlspecialchars($post['excerpt']);
            } else {
                $rendered = $this->filter('post_content', $post['content'], $post);
                $plain = trim(strip_tags($rendered));
                echo htmlspecialchars(mb_substr($plain, 0, 180)) . (mb_strlen($plain) > 180 ? '…' : '');
            }
            ?>
          </div>

          <div class="card-action">
            <a href="/post/<?= $this->e($post['slug']) ?>" class="tech-link">read_post() &rarr;</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination-wrap" aria-label="Pagination">
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?= $currentPage - 1 ?>" class="page-link">&lt; prev_page</a>
        <?php else: ?>
          <span class="page-disabled">&lt; prev_page</span>
        <?php endif; ?>

        <span class="page-status">[ <?= $currentPage ?> / <?= $totalPages ?> ]</span>

        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>" class="page-link">next_page &gt;</a>
        <?php else: ?>
          <span class="page-disabled">next_page &gt;</span>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
