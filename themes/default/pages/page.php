<?php $this->layout('layouts/site', ['title' => $page['title']]) ?>

<article class="card" style="max-width: 800px; margin: 0 auto;">
  <h1 class="mb-2"><?= htmlspecialchars($page['title']) ?></h1>
  <div class="post-content" style="font-size: 1.05rem; line-height: 1.8;">
    <?= $this->filter('post_content', $page['content'], $page) ?>
  </div>
  <div class="mt-4">
    <a href="/" class="button button-light"><i data-lucide="home"></i> Home</a>
  </div>
</article>
