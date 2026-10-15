<?php
$this->layout('layouts/site', ['title' => $post['title']]);
$wordCount = str_word_count(strip_tags((string) $post['content']));
$readTime = max(1, (int) ceil($wordCount / 200));
?>

<article class="single-article">
  <header class="article-header">
    <div class="article-meta">
      <span class="mono-date">pub: <?= date('Y-m-d', (int) $post['created_at']) ?></span>
      <span class="meta-separator">|</span>
      <span class="mono-meta">time: <?= $readTime ?> min</span>
      <?php if ($this->is_admin()): ?>
        <span class="meta-separator">|</span>
        <a href="/admin/edit/<?= (int) $post['id'] ?>" class="card-edit">admin_edit()</a>
      <?php endif; ?>
    </div>
    <h1 class="article-title"><?= $this->e($post['title']) ?></h1>
  </header>

  <div class="article-body markdown-content">
    <?= $this->filter('post_content', $post['content'], $post) ?>
  </div>

  <?php if (!empty($post['tags']) && is_array($post['tags'])): ?>
    <div class="tech-tags" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1.25rem;">
      <?php foreach ($post['tags'] as $tag): ?>
        <span style="font-family: var(--font-mono); font-size: 0.78rem; color: var(--accent); background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); padding: 0.15rem 0.45rem; border-radius: 3px;">#<?= $this->e($tag) ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <footer class="article-footer">
    <div class="back-wrap">
      <a href="/" class="tech-link">&lt;= return_to_index()</a>
    </div>
  </footer>
</article>
