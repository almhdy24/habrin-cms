<?php $this->layout('layouts/site', ['title' => $page['title']]) ?>

<article class="story-article static-page">
  <header class="article-header">
    <h1 class="article-title"><?= $this->e($page['title']) ?></h1>
  </header>

  <div class="article-content typography">
    <?= $this->filter('post_content', $page['content'], $page) ?>
  </div>

  <footer class="article-footer">
    <hr class="divider">
    <div class="footer-actions">
      <a href="/" class="back-link">&larr; Return home</a>
    </div>
  </footer>
</article>
