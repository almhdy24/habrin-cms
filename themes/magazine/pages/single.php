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
