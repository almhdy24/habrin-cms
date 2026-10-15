<?php $this->layout('layouts/admin', ['title' => 'Dashboard']) ?>

<section>

  <!-- Welcome Banner -->
  <div class="dashboard-welcome">
    <div>
      <h2>Welcome back to Habrin CMS</h2>
      <p>Your flat-file content platform is running smoothly. Fast, private, and database-free.</p>
    </div>
    <div class="dashboard-welcome-actions">
      <a href="/admin/add" class="button button-primary">
        <i data-lucide="plus-circle"></i> Write New Post
      </a>
      <a href="/admin/media" class="button button-light">
        <i data-lucide="upload-cloud"></i> Media Library
      </a>
    </div>
  </div>

  <!-- Metric Widgets -->
  <div class="dashboard-widgets">
    <div class="widget">
      <div class="widget-icon-wrap widget-icon-success">
        <i data-lucide="file-check"></i>
      </div>
      <p class="heading">Published Articles</p>
      <p class="title"><?= (int) $postsCount ?></p>
      <div class="widget-footer">
        <a href="/admin?status=published">View all published &rarr;</a>
      </div>
    </div>

    <div class="widget">
      <div class="widget-icon-wrap widget-icon-warning">
        <i data-lucide="edit-3"></i>
      </div>
      <p class="heading">Active Drafts</p>
      <p class="title"><?= (int) $draftCount ?></p>
      <div class="widget-footer">
        <?php if ($draftCount > 0): ?>
          <a href="/admin?status=draft">Review drafts &rarr;</a>
        <?php else: ?>
          <span class="text-muted">No drafts pending</span>
        <?php endif; ?>
      </div>
    </div>

    <div class="widget">
      <div class="widget-icon-wrap widget-icon-primary">
        <i data-lucide="globe"></i>
      </div>
      <p class="heading">Website Title</p>
      <p class="title" style="font-size: 1.3rem; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($settings['site_title'] ?? 'Habrin Blog') ?></p>
      <div class="widget-footer">
        <a href="/" target="_blank" rel="noopener">Visit public site &rarr;</a>
      </div>
    </div>

    <div class="widget">
      <div class="widget-icon-wrap widget-icon-danger">
        <i data-lucide="trash-2"></i>
      </div>
      <p class="heading">Trashed Posts</p>
      <p class="title"><?= (int) $trashCount ?></p>
      <div class="widget-footer">
        <?php if ($trashCount > 0): ?>
          <a href="/admin/trash" class="has-text-danger">Inspect trash (<?= (int) $trashCount ?>) &rarr;</a>
        <?php else: ?>
          <span class="text-muted">Trash is clean</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Quick Action Navigation -->
  <div class="card mb-4" style="padding: 1rem 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
      <span style="font-weight: 700; color: var(--text-light); font-size: 0.9rem;">Quick Navigation:</span>
      <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
        <a href="/admin" class="button button-sm button-light"><i data-lucide="list"></i> All Posts</a>
        <a href="/admin/pages" class="button button-sm button-light"><i data-lucide="file"></i> Pages</a>
        <a href="/admin/media" class="button button-sm button-light"><i data-lucide="image"></i> Media</a>
        <a href="/admin/backup" class="button button-sm button-light"><i data-lucide="archive"></i> Backups</a>
        <a href="/admin/settings" class="button button-sm button-light"><i data-lucide="settings"></i> Settings</a>
      </div>
    </div>
  </div>

  <!-- Recent Posts Header -->
  <div class="flex items-center justify-between mb-3">
    <h2>Recent Content</h2>
    <a href="/admin" class="button button-sm button-light">View All &rarr;</a>
  </div>

  <?php if (!empty($posts)): ?>
    <div class="posts-grid">
      <?php foreach (array_slice(array_reverse($posts, true), 0, 4, true) as $id => $post): ?>
        <?php $isDraft = !empty($post['draft']) || (isset($post['status']) && $post['status'] === 'draft'); ?>
        <div class="post-card">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.4rem;">
            <h3><?= htmlspecialchars($post['title'] ?? 'Untitled') ?></h3>
            <span class="text-muted" style="font-size: 0.75rem; white-space: nowrap;">
              <?= date('M j, Y', (int) ($post['created_at'] ?? time())) ?>
            </span>
          </div>

          <div style="margin-bottom: 0.75rem;">
            <?php if ($isDraft): ?>
              <span class="badge badge-warning">Draft</span>
            <?php else: ?>
              <span class="badge badge-success">Published</span>
            <?php endif; ?>
          </div>

          <div class="excerpt">
            <?= htmlspecialchars(mb_substr(strip_tags($post['content'] ?? ''), 0, 110)) ?>…
          </div>

          <div class="actions">
            <a href="/admin/edit/<?= (int) $id ?>" class="button button-sm button-light">
              <i data-lucide="edit-2"></i> Edit
            </a>
            <a href="/post/<?= htmlspecialchars($post['slug'] ?? '') ?>" class="button button-sm button-light" target="_blank" rel="noopener">
              <i data-lucide="eye"></i> Preview
            </a>
            <form method="POST" action="/admin/delete/<?= (int) $id ?>" style="display:inline; margin-left: auto;">
              <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
              <button type="submit" class="button button-sm button-light" style="color: var(--danger);" title="Move to Trash" onclick="return confirm('Move this post to trash?')">
                <i data-lucide="trash-2"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="card text-center" style="padding: 3rem 1.5rem;">
      <i data-lucide="file-plus" style="width: 48px; height: 48px; color: var(--primary); margin: 0 auto 1rem;"></i>
      <h3>No posts published yet</h3>
      <p class="text-muted" style="max-width: 400px; margin: 0 auto 1.5rem;">Create your first Markdown article with Habrin CMS and start sharing your ideas.</p>
      <a href="/admin/add" class="button button-primary">
        <i data-lucide="plus-circle"></i> Create Your First Post
      </a>
    </div>
  <?php endif; ?>

</section>
