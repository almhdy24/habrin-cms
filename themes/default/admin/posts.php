<?php $this->layout('layouts/admin', ['title' => 'Posts']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="file-text"></i> Post Management</h2>
      <div class="flex flex-wrap gap-1" style="align-items: center; margin-top: 0.5rem;">
        <a href="/admin" class="button button-sm <?= empty($statusFilter) && empty($searchQuery) ? 'button-primary' : 'button-light' ?>">
          All (<?= (int) ($debugAllCount ?? 0) ?>)
        </a>
        <a href="/admin?status=published" class="button button-sm <?= ($statusFilter ?? '') === 'published' ? 'button-primary' : 'button-light' ?>">
          Published (<?= (int) ($debugPublishedCount ?? 0) ?>)
        </a>
        <a href="/admin?status=draft" class="button button-sm <?= ($statusFilter ?? '') === 'draft' ? 'button-primary' : 'button-light' ?>">
          Drafts (<?= (int) ($draftsCount ?? 0) ?>)
        </a>
        <a href="/admin/trash" class="button button-sm button-light">
          Trash (<?= (int) ($trashCount ?? 0) ?>)
        </a>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <form method="GET" action="/admin" class="flex gap-1" style="align-items: center;">
        <div style="position: relative;">
          <input type="text" name="q" value="<?= htmlspecialchars((string) ($searchQuery ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Search posts..." class="input" style="padding: 0.45rem 0.75rem; font-size: 0.9rem; width: 200px;">
        </div>
        <button type="submit" class="button button-sm button-light" title="Search"><i data-lucide="search"></i></button>
        <?php if (!empty($searchQuery)): ?>
          <a href="/admin" class="button button-sm button-light" title="Clear search"><i data-lucide="x"></i></a>
        <?php endif; ?>
      </form>

      <a href="/admin/add" class="button button-primary">
        <i data-lucide="plus-circle"></i> New Post
      </a>
    </div>
  </div>

  <?php if (!empty($searchQuery)): ?>
    <div class="notification notification-info mb-3">
      <i data-lucide="search"></i>
      <span>Search results for: <strong><?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?></strong> (<?= count($posts) ?> found)</span>
    </div>
  <?php endif; ?>

  <?php if (empty($posts)): ?>
    <div class="card text-center" style="padding: 3.5rem 1rem;">
      <i data-lucide="file-text" style="width: 52px; height: 52px; color: var(--text-muted); margin: 0 auto 1rem;"></i>
      <?php if (!empty($searchQuery)): ?>
        <h3 class="mb-1">No matching articles</h3>
        <p class="text-muted" style="max-width: 360px; margin: 0 auto 1rem;">No posts matched your search term "<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>".</p>
        <a href="/admin" class="button button-light">Clear Search</a>
      <?php elseif (($statusFilter ?? '') === 'draft'): ?>
        <h3 class="mb-1">No drafts in progress</h3>
        <p class="text-muted" style="max-width: 360px; margin: 0 auto 1.5rem;">All your posts are currently published and live.</p>
        <a href="/admin/add" class="button button-primary"><i data-lucide="plus-circle"></i> Create a Draft</a>
      <?php else: ?>
        <h3 class="mb-1">No posts found</h3>
        <p class="text-muted" style="max-width: 360px; margin: 0 auto 1.5rem;">Get started by writing your first article.</p>
        <a href="/admin/add" class="button button-primary"><i data-lucide="plus-circle"></i> Create Your First Post</a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th style="min-width: 220px;">Title & Slug</th>
            <th style="width: 110px;">Status</th>
            <th>Excerpt</th>
            <th style="width: 130px;">Published</th>
            <th style="width: 140px; text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $id => $post): ?>
            <?php
            if (!is_array($post)) continue;
            $postId = (int) ($post['id'] ?? $id);
            $title = (string) ($post['title'] ?? 'Untitled');
            $content = (string) ($post['content'] ?? '');
            $status = (string) ($post['status'] ?? 'published');
            $isDraft = $status === 'draft' || !empty($post['draft']);
            $createdAt = isset($post['created_at']) ? (int) $post['created_at'] : 0;
            $slug = (string) ($post['slug'] ?? '');
            $plain = strip_tags($content);
            $plain = preg_replace('/\s+/u', ' ', trim($plain)) ?? '';
            $preview = mb_substr($plain, 0, 75, 'UTF-8');
            if (mb_strlen($plain, 'UTF-8') > 75) $preview .= '…';
            $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safePreview = htmlspecialchars($preview, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safeSlug = htmlspecialchars($slug, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safeDate = $createdAt > 0 ? htmlspecialchars(date('M j, Y', $createdAt), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '—';
            $csrfToken = htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            ?>
            <tr>
              <td>
                <a href="/admin/edit/<?= $postId ?>" style="font-weight: 700; color: var(--text); font-size: 1rem;">
                  <?= $safeTitle ?>
                </a>
                <div style="font-size: 0.78rem; color: var(--text-muted); font-family: var(--font-mono); margin-top: 0.15rem;">
                  /post/<?= $safeSlug ?>
                </div>
              </td>
              <td>
                <?php if ($isDraft): ?>
                  <span class="badge badge-warning">Draft</span>
                <?php else: ?>
                  <span class="badge badge-success">Published</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="text-muted" style="font-size: 0.88rem;" title="<?= $safePreview ?>">
                  <?= $safePreview ?: '—' ?>
                </span>
              </td>
              <td style="font-size: 0.88rem; color: var(--text-light);">
                <?php if ($createdAt > 0): ?>
                  <time datetime="<?= date('c', $createdAt) ?>"><?= $safeDate ?></time>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <div class="flex gap-1" style="justify-content: flex-end;">
                  <a href="/admin/edit/<?= $postId ?>" class="button button-sm button-light" title="Edit Article">
                    <i data-lucide="edit-2"></i>
                  </a>
                  <?php if ($slug !== ''): ?>
                    <a href="/post/<?= $safeSlug ?>" class="button button-sm button-light" target="_blank" rel="noopener noreferrer" title="Preview Article">
                      <i data-lucide="eye"></i>
                    </a>
                  <?php endif; ?>
                  <form method="POST" action="/admin/delete/<?= $postId ?>" style="display:inline;" onsubmit="return confirm('Move this post to trash?')">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" class="button button-sm button-light" style="color: var(--danger);" title="Move to Trash">
                      <i data-lucide="trash-2"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
      <?php $this->insert('partials/pagination', ['currentPage' => $currentPage, 'totalPages' => $totalPages, 'basePath' => '/admin']); ?>
    <?php endif; ?>
  <?php endif; ?>
</section>
