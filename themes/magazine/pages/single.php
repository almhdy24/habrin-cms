<?php $this->layout('layouts/site', ['title' => $post['title']]) ?>

<article class="broadsheet-article">
  <header class="article-header">
    <div class="article-kicker">Chronicle Report</div>
    <h1 class="article-headline"><?= $this->e($post['title']) ?></h1>
    <div class="article-dateline">
      <span class="dateline-item">Published: <?= date('l, F j, Y', (int) $post['created_at']) ?></span>
      <span class="dateline-sep">&bull;</span>
      <span class="dateline-item">By The Editorial Desk</span>
      <?php if ($this->is_admin()): ?>
        <span class="dateline-sep">&bull;</span>
        <a href="/admin/edit/<?= (int) ($post['id'] ?? 0) ?>" class="edit-link">Edit Story</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="article-text editorial-typography">
    <?= $this->filter('post_content', $post['content'], $post) ?>
  </div>

  <?php if (!empty($post['tags']) && is_array($post['tags'])): ?>
    <div class="editorial-tags" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1.5rem; font-family: sans-serif;">
      <?php foreach ($post['tags'] as $tag): ?>
        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; background: #e2e8f0; padding: 0.2rem 0.5rem; font-weight: 600;">#<?= $this->e($tag) ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <footer class="article-footer">
    <div class="footer-signoff">
      <span class="signoff-mark">&#9632;</span>
      <p class="signoff-text">Reported and distributed via Habrin CMS.</p>
    </div>
    <div class="footer-return">
      <a href="/" class="return-link">&larr; Return to Front Page</a>
    </div>
  </footer>
</article>
