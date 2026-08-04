<?php $this->layout('layouts/site', ['title' => ($settings['site_title'] ?? 'Dark')]) ?>

<section class="posts">
  <?php if (empty($posts)): ?>
    <div class="no-posts">
      <h2>No posts yet</h2>
      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
        <p><a href="/admin/add">Create your first post</a></p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <?php foreach ($posts as $postId => $post): ?>
      <article class="post-card">
        <div class="post-meta">
          <time datetime="<?= date('c', (int) $post['created_at']) ?>"><?= date('M j, Y', (int) $post['created_at']) ?></time>
          <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <a href="/admin/edit/<?= (int) $postId ?>" class="edit-link">Edit</a>
          <?php endif; ?>
        </div>
        <h2><a href="/post/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a></h2>
        <div class="post-excerpt">
          <?php
          $rendered = $this->filter('post_content', $post['content'], $post);
          $plain = strip_tags($rendered);
          echo htmlspecialchars(mb_substr($plain, 0, 160)) . '…';
          ?>
        </div>
        <a href="/post/<?= $this->e($post['slug']) ?>" class="read-more">Read more →</a>
      </article>
    <?php endforeach; ?>

    <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?= $currentPage - 1 ?>">← Previous</a>
        <?php endif; ?>
        <span><?= $currentPage ?> / <?= $totalPages ?></span>
        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>">Next →</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</section>
