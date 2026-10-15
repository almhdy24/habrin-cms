<?php $this->layout('layouts/site', ['title' => ($settings['site_title'] ?? 'CleanBlog')]) ?>

<section class="posts-feed">
  <?php if (empty($posts)): ?>
    <div class="empty-feed">
      <h2>No articles published yet</h2>
      <p class="text-muted">Stories and essays will appear here as soon as they are published.</p>
      <?php if ($this->is_admin()): ?>
        <p class="mt-3"><a href="/admin/add" class="button-story"><i data-lucide="plus"></i> Write Your First Story</a></p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <?php foreach ($posts as $postId => $post): ?>
      <?php
      $wordCount = str_word_count(strip_tags((string) $post['content']));
      $readTime = max(1, (int) ceil($wordCount / 200));
      ?>
      <article class="story-card">
        <header class="story-header">
          <div class="story-meta">
            <time datetime="<?= date('c', (int) $post['created_at']) ?>"><?= date('F j, Y', (int) $post['created_at']) ?></time>
            <span class="meta-dot">&bull;</span>
            <span class="reading-time"><?= $readTime ?> min read</span>
            <?php if ($this->is_admin()): ?>
              <span class="meta-dot">&bull;</span>
              <a href="/admin/edit/<?= (int) $postId ?>" class="edit-link">Edit</a>
            <?php endif; ?>
          </div>
          <h2 class="story-title">
            <a href="/post/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a>
          </h2>
        </header>

        <div class="story-summary">
          <?php
          $rendered = $this->filter('post_content', $post['content'], $post);
          $plain = trim(strip_tags($rendered));
          echo htmlspecialchars(mb_substr($plain, 0, 220)) . (mb_strlen($plain) > 220 ? '…' : '');
          ?>
        </div>

        <div class="story-footer">
          <a href="/post/<?= $this->e($post['slug']) ?>" class="read-link">Read full story &rarr;</a>
        </div>
      </article>
    <?php endforeach; ?>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination-wrap" aria-label="Pagination Navigation">
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?= $currentPage - 1 ?>" class="page-nav-link">&larr; Newer stories</a>
        <?php else: ?>
          <span class="page-nav-disabled">&larr; Newer stories</span>
        <?php endif; ?>

        <span class="page-current">Page <?= $currentPage ?> of <?= $totalPages ?></span>

        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>" class="page-nav-link">Older stories &rarr;</a>
        <?php else: ?>
          <span class="page-nav-disabled">Older stories &rarr;</span>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
