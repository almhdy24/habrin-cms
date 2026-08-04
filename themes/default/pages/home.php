<?php $this->layout('layouts/site', ['title' => $settings['site_title'] ?? 'Home']) ?>

<!-- Hero Section -->
<section class="hero-home">
  <h1><?= $this->e($settings['hero_heading'] ?? 'Welcome to Pico CMS') ?></h1>
  <p><?= $this->e($settings['hero_description'] ?? 'A lightweight flat-file CMS built with PHP and JSON.') ?></p>
  <?php if (!empty($settings['hero_button_text']) && !empty($settings['hero_button_link'])): ?>
    <a href="<?= $this->e($settings['hero_button_link']) ?>" class="button">
      <?= $this->e($settings['hero_button_text']) ?> <i data-lucide="arrow-right"></i>
    </a>
  <?php endif; ?>
</section>

<!-- Posts Section -->
<section id="posts">
  <div class="section-title">
    <i data-lucide="file-text"></i>
    <span>Latest Posts</span>
  </div>
  <?php if (!empty($posts)): ?>
    <p class="section-subtitle">
      Showing <?= number_format($start) ?>–<?= number_format($end) ?>
      of <?= number_format($totalPosts) ?> post<?= $totalPosts != 1 ? 's' : '' ?>
    </p>
  <?php else: ?>
    <p class="section-subtitle">No posts yet.</p>
  <?php endif; ?>

  <?php if (empty($posts)): ?>
    <div class="empty-state">
      <i data-lucide="file-text" style="width: 48px; height: 48px;"></i>
      <h3>No posts yet</h3>
      <p class="text-muted">The first post will appear here.</p>
      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
        <a href="/admin/add" class="button button-primary mt-3">
          <i data-lucide="plus-circle"></i> Create First Post
        </a>
      <?php endif; ?>
    </div>
  <?php else: ?>

    <div class="posts-grid">
      <?php foreach ($posts as $postId => $post): ?>
        <article class="post-card">
          <div class="post-meta">
            <span class="date">
              <i data-lucide="calendar"></i>
              <?= date('F j, Y', (int) $post['created_at']) ?>
            </span>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
              <a href="/admin/edit/<?= (int) $postId ?>" class="button button-sm button-light" title="Edit">
                <i data-lucide="edit" style="width:14px;height:14px;"></i>
              </a>
            <?php endif; ?>
          </div>

          <h3>
            <a href="/post/<?= htmlspecialchars($post['slug']) ?>">
              <?= htmlspecialchars($post['title']) ?>
            </a>
          </h3>

          <div class="excerpt">
            <?php
            $rendered = $this->filter('post_content', $post['content'], $post);
            $plain = strip_tags($rendered);
            echo htmlspecialchars(mb_substr($plain, 0, 180)) . '…';
            ?>
          </div>

          <div class="actions">
            <a href="/post/<?= htmlspecialchars($post['slug']) ?>" class="button button-primary button-sm">
              Read More <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
      <?php $this->insert('partials/pagination', compact('currentPage', 'totalPages')) ?>
    <?php endif; ?>

  <?php endif; ?>
</section>
