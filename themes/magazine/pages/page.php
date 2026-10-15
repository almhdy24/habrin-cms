<?php $this->layout('layouts/site', ['title' => $page['title']]) ?>

<article class="broadsheet-article static-page">
  <header class="article-header">
    <div class="article-kicker">Special Section</div>
    <h1 class="article-headline"><?= $this->e($page['title']) ?></h1>
  </header>

  <div class="article-text editorial-typography">
    <?= $this->filter('post_content', $page['content'], $page) ?>
  </div>

  <footer class="article-footer">
    <div class="footer-return">
      <a href="/" class="return-link">&larr; Return to Front Page</a>
    </div>
  </footer>
</article>
