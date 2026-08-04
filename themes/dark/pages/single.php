<?php $this->layout('layouts/site', ['title' => $post['title']]) ?>

<article class="post-single">
  <header>
    <div class="post-meta">
      <time datetime="<?= date('c', (int) $post['created_at']) ?>"><?= date('F j, Y', (int) $post['created_at']) ?></time>
      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
        <a href="/admin/edit/<?= (int) $post['id'] ?>" class="edit-link">Edit</a>
      <?php endif; ?>
    </div>
    <h1><?= $this->e($post['title']) ?></h1>
  </header>
  <div class="post-content">
    <?= $this->filter('post_content', $post['content'], $post) ?>
  </div>
  <footer>
    <a href="/" class="back-link">← Back to home</a>
  </footer>
</article>
