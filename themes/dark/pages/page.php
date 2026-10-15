<?php $this->layout('layouts/site', ['title' => $page['title']]) ?>

<article class="single-article static-page">
  <header class="article-header">
    <div class="article-meta">
      <span class="mono-date">type: static_page</span>
    </div>
    <h1 class="article-title"><?= $this->e($page['title']) ?></h1>
  </header>

  <div class="article-body markdown-content">
    <?= $this->filter('post_content', $page['content'], $page) ?>
  </div>

  <footer class="article-footer">
    <div class="back-wrap">
      <a href="/" class="tech-link">&lt;= return_to_index()</a>
    </div>
  </footer>
</article>
