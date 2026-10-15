<?php $this->layout('layouts/admin', ['title' => 'Dashboard']) ?>

<section>

  <!-- Widgets -->
  <div class="dashboard-widgets">
    <div class="widget">
      <i data-lucide="file-text" style="width: 32px; height: 32px; color: var(--primary); margin-bottom: 0.5rem;"></i>
      <p class="heading">Published Posts</p>
      <p class="title"><?= (int) $postsCount ?></p>
      <div class="text-muted" style="font-size: 0.8rem;">
        <a href="/admin?status=published">View published &rarr;</a>
      </div>
    </div>

    <div class="widget">
      <i data-lucide="edit-3" style="width: 32px; height: 32px; color: #d97706; margin-bottom: 0.5rem;"></i>
      <p class="heading">Draft Posts</p>
      <p class="title"><?= (int) $draftCount ?></p>
      <div class="text-muted" style="font-size: 0.8rem;">
        <?php if ($draftCount > 0): ?>
          <a href="/admin?status=draft">View drafts &rarr;</a>
        <?php else: ?>
          No active drafts
        <?php endif; ?>
      </div>
    </div>

    <div class="widget">
      <i data-lucide="type" style="width: 32px; height: 32px; color: var(--primary); margin-bottom: 0.5rem;"></i>
      <p class="heading">Site Title</p>
      <p class="subtitle" style="font-size: 1.1rem; font-weight: 600; margin-top: 0.3rem;"><?= htmlspecialchars($settings['site_title'] ?? 'My Blog') ?></p>
      <div class="text-muted" style="font-size: 0.8rem;">
        <a href="/" target="_blank">Visit site &rarr;</a>
      </div>
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
    <a href="/admin/media" class="button button-light">
      <i data-lucide="image"></i> Media Library
    </a>
    <a href="/admin/backup" class="button button-light">
      <i data-lucide="archive"></i> Backups
    </a>
    <a href="/admin/settings" class="button button-light">
      <i data-lucide="settings"></i> Settings
    </a>
  </div>

  <!-- Recent Posts -->
  <h2 class="mb-3">Recent Posts</h2>

  <?php if (!empty($posts)): ?>
    <div class="posts-grid">
      <?php foreach (array_slice(array_reverse($posts, true), 0, 4, true) as $id => $post): ?>
        <?php $isDraft = !empty($post['draft']) || (isset($post['status']) && $post['status'] === 'draft'); ?>
        <div class="post-card">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
            <h3 style="margin-bottom: 0.3rem; font-size: 1.1rem;"><?= htmlspecialchars($post['title']) ?></h3>
            <span class="text-muted" style="font-size: 0.75rem; white-space: nowrap;">
              <?= date('M j, Y', (int) ($post['created_at'] ?? time())) ?>
            </span>
          </div>

          <div style="margin: 0.3rem 0 0.6rem 0;">
            <?php if ($isDraft): ?>
              <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">Draft</span>
            <?php else: ?>
              <span class="badge" style="background: #dcfce7; color: #166534; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">Published</span>
            <?php endif; ?>
          </div>

          <div class="excerpt" style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.75rem;">
            <?= htmlspecialchars(mb_substr(strip_tags($post['content'] ?? ''), 0, 100)) ?>…
          </div>

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
