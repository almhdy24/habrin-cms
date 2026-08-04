<?php $this->layout('layouts/admin', ['title' => 'Posts']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-0"><i data-lucide="file-text"></i> All Posts</h2>
      <?php if (isset($debugPublishedCount)): ?>
        <p class="text-muted" style="margin-top:0.25rem;font-size:0.9rem;">
          <?= (int) $debugPublishedCount ?> published posts
        </p>
      <?php endif; ?>
    </div>
    <a href="/admin/add" class="button button-primary">
      <i data-lucide="plus-circle"></i> New Post
    </a>
  </div>

  <?php if (empty($posts)): ?>
    <div class="notification notification-success">
      <i data-lucide="file-text"></i>
      <span>No posts found. <a href="/admin/add">Create your first post</a></span>
    </div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th style="min-width:180px;">Title</th>
            <th>Preview</th>
            <th style="min-width:100px;">Date</th>
            <th style="min-width:130px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $id => $post): ?>
            <?php
            if (!is_array($post)) continue;
            $postId = (int) $id;
            $title = (string) ($post['title'] ?? 'Untitled');
            $content = (string) ($post['content'] ?? '');
            $createdAt = isset($post['created_at']) ? (int) $post['created_at'] : 0;
            $slug = (string) ($post['slug'] ?? '');
            $plain = strip_tags($content);
            $plain = preg_replace('/\s+/u', ' ', trim($plain)) ?? '';
            $preview = mb_substr($plain, 0, 80, 'UTF-8');
            if (mb_strlen($plain, 'UTF-8') > 80) $preview .= '…';
            $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safePreview = htmlspecialchars($preview, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safeSlug = htmlspecialchars($slug, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safeDate = $createdAt > 0 ? htmlspecialchars(date('M j, Y', $createdAt), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '—';
            $csrfToken = htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            ?>
            <tr>
              <td><strong><?= $safeTitle ?></strong></td>
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
                    <a href="/post/<?= $safeSlug ?>" class="button button-sm button-light" target="_blank" rel="noopener noreferrer" title="View post"><i data-lucide="eye"></i></a>
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
