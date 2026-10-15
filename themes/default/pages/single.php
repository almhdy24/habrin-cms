<?php $this->layout('layouts/site', ['title' => $post['title']]) ?>

<article class="card" style="max-width: 800px; margin: 0 auto;">

  <div class="flex flex-wrap items-center justify-between gap-1 mb-3">
    <a href="/" class="button button-light button-sm">
      <i data-lucide="arrow-left"></i> Back
    </a>
    <span class="text-muted" style="font-size: 0.9rem;">
      <i data-lucide="calendar"></i> <?= date('F j, Y', (int) $post['created_at']) ?>
    </span>
  </div>

  <?php if (!empty($isDraft)): ?>
    <div class="notification notification-warning mb-3" style="background:#fff3cd;color:#856404;border:1px solid #ffeeba;padding:0.75rem 1rem;border-radius:4px;display:flex;align-items:center;gap:0.5rem;">
      <i data-lucide="alert-triangle"></i>
      <span><strong>Draft Preview:</strong> This post is an unpublished draft and is only visible to logged-in administrators.</span>
    </div>
  <?php endif; ?>

  <h1 class="mb-2"><?= htmlspecialchars($post['title']) ?></h1>

  <div class="post-content" style="font-size: 1.05rem; line-height: 1.8;">
    <?= $this->filter('post_content', $post['content'], $post) ?>
  </div>

  <div class="mt-4">
    <a href="/" class="button button-light">
      <i data-lucide="home"></i> Home
    </a>
  </div>

</article>
