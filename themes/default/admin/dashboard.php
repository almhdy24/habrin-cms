<?php $this->layout('layouts/admin', ['title' => 'Dashboard']) ?>

<section>

  <!-- Widgets -->
  <div class="dashboard-widgets">
    <div class="widget">
      <i data-lucide="file-text" style="width: 32px; height: 32px; color: var(--primary); margin-bottom: 0.5rem;"></i>
      <p class="heading">Total Posts</p>
      <p class="title"><?= (int) $postsCount ?></p>
      <div class="text-muted" style="font-size: 0.8rem;">
        <?php if ($postsCount === 0): ?>
          No posts yet.
        <?php else: ?>
          <?= $postsCount ?> published
        <?php endif; ?>
      </div>
    </div>

    <div class="widget">
      <i data-lucide="type" style="width: 32px; height: 32px; color: var(--primary); margin-bottom: 0.5rem;"></i>
      <p class="heading">Site Title</p>
      <p class="subtitle"><?= htmlspecialchars($settings['site_title'] ?? 'My Blog') ?></p>
    </div>

    <div class="widget">
      <i data-lucide="trash-2" style="width: 32px; height: 32px; color: var(--danger); margin-bottom: 0.5rem;"></i>
      <p class="heading">Trashed Posts</p>
      <p class="title"><?= (int) $trashCount ?></p>
      <?php if ($trashCount > 0): ?>
        <a href="/admin/trash" class="button button-sm button-danger mt-2">View Trash</a>
      <?php else: ?>
        <div class="text-muted" style="font-size: 0.8rem;">Trash is empty</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="flex flex-wrap gap-1 mb-4">
    <a href="/admin/add" class="button button-primary">
      <i data-lucide="plus-circle"></i> New Post
    </a>
    <a href="/admin" class="button button-light">
      <i data-lucide="list"></i> All Posts
    </a>
    <a href="/admin/settings" class="button button-light">
      <i data-lucide="settings"></i> Settings
    </a>
  </div>

  <!-- Recent Posts -->
  <h2 class="mb-3">Recent Posts</h2>

  <?php if (!empty($posts)): ?>
    <div class="posts-grid">
      <?php foreach (array_slice(array_reverse($posts), 0, 3, true) as $id => $post): ?>
        <div class="post-card">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
            <h3 style="margin-bottom: 0.3rem;"><?= htmlspecialchars($post['title']) ?></h3>
            <span class="text-muted" style="font-size: 0.7rem; white-space: nowrap;">
              <?= date('M j', (int) $post['created_at']) ?>
            </span>
          </div>
          <div class="excerpt"><?= htmlspecialchars(mb_substr($post['content'], 0, 100)) ?>…</div>
          <div class="actions">
            <a href="/admin/edit/<?= (int) $id ?>" class="button button-sm button-light">
              <i data-lucide="edit"></i> Edit
            </a>
            <a href="/post/<?= htmlspecialchars($post['slug']) ?>" class="button button-sm button-light" target="_blank">
              <i data-lucide="eye"></i> View
            </a>
            <form method="POST" action="/admin/delete/<?= (int) $id ?>" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
              <button type="submit" class="button button-sm button-danger" onclick="return confirm('Delete this post?')">
                <i data-lucide="trash-2"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="notification notification-success">
      <i data-lucide="file-text"></i>
      <span>No posts yet. <a href="/admin/add">Create your first post</a></span>
    </div>
  <?php endif; ?>

</section>
