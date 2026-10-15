<?php $this->layout('layouts/admin', ['title' => 'Posts']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="file-text"></i> Posts</h2>
      <div class="flex flex-wrap gap-2 text-muted" style="font-size:0.85rem;align-items:center;">
        <a href="/admin" class="button button-sm <?= empty($statusFilter) && empty($searchQuery) ? 'button-primary' : 'button-light' ?>" style="padding:0.2rem 0.6rem;">
          All (<?= (int) ($debugAllCount ?? 0) ?>)
        </a>
        <a href="/admin" class="button button-sm button-light" style="padding:0.2rem 0.6rem;">
          Published (<?= (int) ($debugPublishedCount ?? 0) ?>)
        </a>
        <a href="/admin?status=draft" class="button button-sm <?= ($statusFilter ?? '') === 'draft' ? 'button-primary' : 'button-light' ?>" style="padding:0.2rem 0.6rem;">
          Drafts (<?= (int) ($draftsCount ?? 0) ?>)
        </a>
        <a href="/admin/trash" class="button button-sm button-light" style="padding:0.2rem 0.6rem;">
          Trash (<?= (int) ($trashCount ?? 0) ?>)
        </a>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <form method="GET" action="/admin" class="flex gap-1" style="align-items:center;">
        <input type="text" name="q" value="<?= htmlspecialchars((string) ($searchQuery ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Search posts..." class="input" style="padding:0.35rem 0.6rem;font-size:0.85rem;max-width:180px;">
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
    <div class="card text-center" style="padding:3rem 1rem;">
      <i data-lucide="file-text" style="width:48px;height:48px;color:var(--text-light);margin-bottom:1rem;"></i>
      <?php if (!empty($searchQuery)): ?>
        <h3 class="mb-1">No matching posts</h3>
        <p class="text-muted">No posts found matching "<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>".</p>
        <a href="/admin" class="button button-light mt-2">Clear Search</a>
      <?php elseif (($statusFilter ?? '') === 'draft'): ?>
        <h3 class="mb-1">No drafts</h3>
        <p class="text-muted">You have no draft posts.</p>
        <a href="/admin/add" class="button button-primary mt-2">Create a Draft</a>
      <?php else: ?>
        <h3 class="mb-1">No posts found</h3>
        <p class="text-muted">Create your first blog post to get started.</p>
        <a href="/admin/add" class="button button-primary mt-2"><i data-lucide="plus-circle"></i> Create Post</a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th style="min-width:180px;">Title</th>
            <th>Status</th>
            <th>Preview</th>
            <th style="min-width:100px;">Date</th>
            <th style="min-width:140px;">Actions</th>
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
            $createdAt = isset($post['created_at']) ? (int) $post['created_at'] : 0;
            $slug = (string) ($post['slug'] ?? '');
            $plain = strip_tags($content);
            $plain = preg_replace('/\s+/u', ' ', trim($plain)) ?? '';
            $preview = mb_substr($plain, 0, 70, 'UTF-8');
            if (mb_strlen($plain, 'UTF-8') > 70) $preview .= '…';
            $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safePreview = htmlspecialchars($preview, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safeSlug = htmlspecialchars($slug, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safeDate = $createdAt > 0 ? htmlspecialchars(date('M j, Y', $createdAt), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '—';
            $csrfToken = htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            ?>
            <tr>
              <td>
                <strong><?= $safeTitle ?></strong>
                <div style="font-size:0.75rem;color:var(--text-light);font-family:monospace;">/post/<?= $safeSlug ?></div>
              </td>
              <td>
                <?php if ($status === 'draft'): ?>
                  <span class="badge" style="background:#fff3cd;color:#856404;border:1px solid #ffeeba;padding:0.2rem 0.5rem;font-size:0.75rem;border-radius:3px;">Draft</span>
                <?php else: ?>
                  <span class="badge" style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;padding:0.2rem 0.5rem;font-size:0.75rem;border-radius:3px;">Published</span>
                <?php endif; ?>
              </td>
              <td><span style="color:var(--text-light);" title="<?= $safePreview ?>"><?= $safePreview ?: '—' ?></span></td>
              <td style="font-size:0.85rem;color:var(--text-light);">
                <?php if ($createdAt > 0): ?>
                  <time datetime="<?= date('c', $createdAt) ?>"><?= $safeDate ?></time>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
              <td>
                <div class="flex flex-wrap gap-1">
                  <a href="/admin/edit/<?= $postId ?>" class="button button-sm button-light" title="Edit post"><i data-lucide="edit"></i></a>
                  <?php if ($slug !== ''): ?>
                    <a href="/post/<?= $safeSlug ?>" class="button button-sm button-light" target="_blank" rel="noopener noreferrer" title="View / Preview post"><i data-lucide="eye"></i></a>
                  <?php endif; ?>
                  <form method="POST" action="/admin/delete/<?= $postId ?>" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" class="button button-sm button-danger" title="Move post to trash" onclick="return confirm('Move this post to trash?')"><i data-lucide="trash-2"></i></button>
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
