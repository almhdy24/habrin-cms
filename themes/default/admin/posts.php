<?php $this->layout('layouts/admin', ['title' => __('posts.title')]) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="file-text"></i> <?= __('posts.title') ?></h2>
      <div class="flex flex-wrap gap-1" style="align-items: center; margin-top: 0.5rem;">
        <a href="/admin" class="button button-sm <?= empty($statusFilter) && empty($searchQuery) ? 'button-primary' : 'button-light' ?>">
          <?= __('posts.filter_all') ?> (<?= (int) ($debugAllCount ?? 0) ?>)
        </a>
        <a href="/admin?status=published" class="button button-sm <?= ($statusFilter ?? '') === 'published' ? 'button-primary' : 'button-light' ?>">
          <?= __('posts.filter_published') ?> (<?= (int) ($debugPublishedCount ?? 0) ?>)
        </a>
        <a href="/admin?status=draft" class="button button-sm <?= ($statusFilter ?? '') === 'draft' ? 'button-primary' : 'button-light' ?>">
          <?= __('posts.filter_drafts') ?> (<?= (int) ($draftsCount ?? 0) ?>)
        </a>
        <a href="/admin/trash" class="button button-sm button-light">
          <?= __('nav.trash') ?> (<?= (int) ($trashCount ?? 0) ?>)
        </a>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <form method="GET" action="/admin" class="flex gap-1" style="align-items: center;">
        <div style="position: relative;">
          <label for="adminSearchInput" class="sr-only"><?= __('posts.filter_search') ?></label>
          <input
            id="adminSearchInput"
            type="text"
            name="q"
            value="<?= htmlspecialchars((string) ($searchQuery ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            placeholder="<?= __('posts.filter_search') ?>"
            class="input"
            style="min-height: 40px; padding: 0.45rem 0.75rem; font-size: 0.9rem; width: 220px;"
          >
        </div>
        <button type="submit" class="button button-sm button-light" style="min-height: 40px;" title="<?= __('common.search') ?>"><i data-lucide="search"></i></button>
        <?php if (!empty($searchQuery)): ?>
          <a href="/admin" class="button button-sm button-light" style="min-height: 40px;" title="<?= __('common.close') ?>"><i data-lucide="x"></i></a>
        <?php endif; ?>
      </form>

      <a href="/admin/add" class="button button-primary">
        <i data-lucide="plus-circle"></i> <?= __('posts.write_new') ?>
      </a>
    </div>
  </div>

  <?php if (!empty($searchQuery)): ?>
    <div class="notification notification-info mb-3">
      <i data-lucide="search"></i>
      <span><?= __('common.search') ?>: <strong><?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?></strong> (<?= count($posts) ?>)</span>
    </div>
  <?php endif; ?>

  <?php if (empty($posts)): ?>
    <div class="card text-center" style="padding: 3.5rem 1rem;">
      <i data-lucide="file-text" style="width: 52px; height: 52px; color: var(--text-muted); margin: 0 auto 1rem;"></i>
      <?php if (!empty($searchQuery)): ?>
        <h3 class="mb-1"><?= __('posts.no_results') ?></h3>
        <p class="text-muted" style="max-width: 360px; margin: 0 auto 1rem;"><?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?></p>
        <a href="/admin" class="button button-light"><?= __('posts.filter_all') ?></a>
      <?php elseif (($statusFilter ?? '') === 'draft'): ?>
        <h3 class="mb-1"><?= __('dashboard.no_drafts') ?></h3>
        <p class="text-muted" style="max-width: 360px; margin: 0 auto 1.5rem;"><?= __('dashboard.tagline') ?></p>
        <a href="/admin/add" class="button button-primary"><i data-lucide="plus-circle"></i> <?= __('posts.write_new') ?></a>
      <?php else: ?>
        <h3 class="mb-1"><?= __('dashboard.no_posts') ?></h3>
        <p class="text-muted" style="max-width: 360px; margin: 0 auto 1.5rem;"><?= __('dashboard.tagline') ?></p>
        <a href="/admin/add" class="button button-primary"><i data-lucide="plus-circle"></i> <?= __('posts.write_new') ?></a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <form method="POST" action="/admin/bulk" id="bulkPostsForm">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      
      <div class="flex flex-wrap items-center justify-between gap-2 mb-3 p-2 card" style="background: var(--bg-card); border-radius: 6px; box-shadow: none; border: 1px solid var(--border);">
        <div class="flex flex-wrap items-center gap-2">
          <label for="bulkActionSelect" class="sr-only"><?= __('posts.bulk_actions') ?></label>
          <select name="bulk_action" id="bulkActionSelect" class="input" style="min-height: 36px; padding: 0.35rem 0.65rem; width: auto; font-size: 0.88rem;">
            <option value=""><?= __('posts.bulk_actions') ?></option>
            <option value="publish"><?= __('posts.bulk_publish') ?></option>
            <option value="draft"><?= __('posts.bulk_draft') ?></option>
            <option value="trash"><?= __('posts.bulk_trash') ?></option>
          </select>
          <button type="submit" id="bulkSubmitBtn" class="button button-sm button-light" disabled>
            <i data-lucide="check-square"></i> <?= __('posts.apply_bulk') ?>
          </button>
          <span id="selectedCountBadge" class="badge badge-neutral" style="display: none;">0</span>
        </div>
      </div>

      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th style="width: 44px; text-align: center;">
                <input type="checkbox" id="selectAllCheckbox" title="<?= __('common.select_all') ?>" aria-label="<?= __('common.select_all') ?>">
              </th>
              <th style="min-width: 220px;"><?= __('posts.article_title') ?></th>
              <th style="width: 110px;"><?= __('common.status') ?></th>
              <th><?= __('posts.excerpt') ?></th>
              <th style="width: 130px;"><?= __('posts.filter_published') ?></th>
              <th style="width: 140px; text-align: right;"><?= __('common.actions') ?></th>
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
                <td style="text-align: center;">
                  <input type="checkbox" name="post_ids[]" value="<?= $postId ?>" class="post-item-checkbox" aria-label="Select <?= $safeTitle ?>">
                </td>
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
                    <span class="badge badge-warning"><?= __('posts.status_draft') ?></span>
                  <?php else: ?>
                    <span class="badge badge-success"><?= __('posts.status_published') ?></span>
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
                    <a href="/admin/edit/<?= $postId ?>" class="button button-sm button-light" title="<?= __('posts.edit_title') ?>">
                      <i data-lucide="edit-2"></i>
                    </a>
                    <?php if ($slug !== ''): ?>
                      <a href="/post/<?= $safeSlug ?>" class="button button-sm button-light" target="_blank" rel="noopener noreferrer" title="<?= __('common.preview') ?>">
                        <i data-lucide="eye"></i>
                      </a>
                    <?php endif; ?>
                    <button type="submit" form="deletePostForm_<?= $postId ?>" class="button button-sm button-light" style="color: var(--danger);" title="<?= __('posts.move_to_trash') ?>">
                      <i data-lucide="trash-2"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </form>

    <?php foreach ($posts as $id => $post): ?>
      <?php
      if (!is_array($post)) continue;
      $postId = (int) ($post['id'] ?? $id);
      $csrfToken = htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      ?>
      <form id="deletePostForm_<?= $postId ?>" method="POST" action="/admin/delete/<?= $postId ?>" style="display:none;" onsubmit="return confirm('<?= __('common.confirm_delete') ?>')">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      </form>
    <?php endforeach; ?>

    <?php if ($totalPages > 1): ?>
      <?php $this->insert('partials/pagination', ['currentPage' => $currentPage, 'totalPages' => $totalPages, 'basePath' => '/admin']); ?>
    <?php endif; ?>
  <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const selectAll = document.getElementById('selectAllCheckbox');
  const checkboxes = document.querySelectorAll('.post-item-checkbox');
  const actionSelect = document.getElementById('bulkActionSelect');
  const submitBtn = document.getElementById('bulkSubmitBtn');
  const badge = document.getElementById('selectedCountBadge');
  const bulkForm = document.getElementById('bulkPostsForm');

  function updateBulkState() {
    const checked = Array.from(checkboxes).filter(cb => cb.checked);
    const count = checked.length;
    const hasAction = actionSelect && actionSelect.value !== '';

    if (badge) {
      if (count > 0) {
        badge.style.display = 'inline-block';
        badge.textContent = count + ' selected';
      } else {
        badge.style.display = 'none';
      }
    }

    if (submitBtn) {
      submitBtn.disabled = !(count > 0 && hasAction);
    }

    if (selectAll) {
      selectAll.checked = checkboxes.length > 0 && count === checkboxes.length;
      selectAll.indeterminate = count > 0 && count < checkboxes.length;
    }
  }

  if (selectAll) {
    selectAll.addEventListener('change', function() {
      checkboxes.forEach(cb => cb.checked = selectAll.checked);
      updateBulkState();
    });
  }

  checkboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkState);
  });

  if (actionSelect) {
    actionSelect.addEventListener('change', updateBulkState);
  }

  if (bulkForm) {
    bulkForm.addEventListener('submit', function(e) {
      if (actionSelect && actionSelect.value === 'trash') {
        if (!confirm('<?= __('common.confirm_delete') ?>')) {
          e.preventDefault();
        }
      }
    });
  }
});
</script>
