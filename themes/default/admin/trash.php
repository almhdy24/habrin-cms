<?php $this->layout('layouts/admin', ['title' => 'Trash']) ?>

<section>

  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <h2 class="mb-0"><i data-lucide="trash-2" style="color: var(--danger);"></i> Trash</h2>
    <a href="/admin" class="button button-light">
      <i data-lucide="arrow-left"></i> Back
    </a>
  </div>

  <?php if (!empty($posts)): ?>
    <div class="notification notification-info mb-4">
      <i data-lucide="info"></i>
      <span>Posts in trash will be automatically deleted after <strong>30 days</strong>.</span>
    </div>

    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Deleted</th>
            <th style="min-width: 220px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $id => $post): ?>
            <?php if (!empty($post['deleted_at'])): ?>
            <tr>
              <td><strong><?= htmlspecialchars($post['title']) ?></strong></td>
              <td>
                <?= date('M j, Y g:i A', (int) $post['deleted_at']) ?>
                <div class="text-muted" style="font-size: 0.8rem;">
                  <?php
                  $days = floor((time() - (int) $post['deleted_at']) / 86400);
                  echo $days . ' day' . ($days != 1 ? 's' : '') . ' ago';
                  ?>
                </div>
              </td>
              <td>
                <div class="flex flex-wrap gap-1">
                  <form method="POST" action="/admin/restore/<?= (int) $id ?>" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="button button-sm button-success" onclick="return confirm('Restore this post?')">
                      <i data-lucide="refresh-cw"></i> Restore
                    </button>
                  </form>
                  <form method="POST" action="/admin/destroy/<?= (int) $id ?>" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="button button-sm button-danger" onclick="return confirm('Permanently delete this post?')">
                      <i data-lucide="trash"></i> Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      <form method="POST" action="/admin/emptyTrash" style="display:inline;" onsubmit="return confirm('Empty all trash? This cannot be undone.')">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="button button-danger">
          <i data-lucide="trash-2"></i> Empty Trash
        </button>
      </form>
    </div>

  <?php else: ?>
    <div class="text-center" style="padding: 3rem 0;">
      <i data-lucide="trash-2" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 1rem;"></i>
      <h3 class="mb-1">Trash is empty</h3>
      <p class="text-muted">No posts in the trash bin.</p>
      <a href="/admin" class="button button-light mt-3">
        <i data-lucide="arrow-left"></i> Back to Posts
      </a>
    </div>
  <?php endif; ?>

</section>
