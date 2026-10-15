<?php
$this->layout('layouts/site', ['title' => $post['title']]);
$wordCount = str_word_count(strip_tags((string) $post['content']));
$readTime = max(1, (int) ceil($wordCount / 200));
?>

<article class="story-article">
  <header class="article-header">
    <div class="article-meta">
      <time datetime="<?= date('c', (int) $post['created_at']) ?>"><?= date('F j, Y', (int) $post['created_at']) ?></time>
      <span class="meta-dot">&bull;</span>
      <span class="reading-time"><?= $readTime ?> min read</span>
      <?php if ($this->is_admin()): ?>
        <span class="meta-dot">&bull;</span>
        <a href="/admin/edit/<?= (int) $post['id'] ?>" class="edit-link">Edit Article</a>
      <?php endif; ?>
    </div>
    <h1 class="article-title"><?= $this->e($post['title']) ?></h1>
  </header>

  <div class="article-content typography">
    <?= $this->filter('post_content', $post['content'], $post) ?>
  </div>

  <?php if (!empty($post['tags']) && is_array($post['tags'])): ?>
    <div class="article-tags" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1.5rem;">
      <?php foreach ($post['tags'] as $tag): ?>
        <span style="font-size: 0.82rem; color: #64748b; background: #f1f5f9; padding: 0.2rem 0.6rem; border-radius: 999px;">#<?= $this->e($tag) ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <footer class="article-footer">
    <hr class="divider">
    <div class="footer-actions">
      <a href="/" class="back-link">&larr; Back to all stories</a>
    </div>
  </footer>
</article>
