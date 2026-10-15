<?php $this->layout('layouts/admin', ['title' => __('nav.dashboard')]) ?>

<section>

  <!-- Welcome Banner -->
  <div class="dashboard-welcome">
    <div>
      <h2><?= __('dashboard.welcome') ?></h2>
      <p><?= __('dashboard.tagline') ?></p>
    </div>
    <div class="dashboard-welcome-actions">
      <a href="/admin/add" class="button button-primary">
        <i data-lucide="plus-circle"></i> <?= __('dashboard.write_new') ?>
      </a>
      <a href="/admin/pages/create" class="button button-light">
        <i data-lucide="file-plus"></i> <?= __('dashboard.new_page') ?>
      </a>
      <a href="/admin/media" class="button button-light">
        <i data-lucide="upload-cloud"></i> <?= __('nav.media') ?>
      </a>
    </div>
  </div>

  <!-- Metric Widgets (6 Cards) -->
  <div class="dashboard-widgets" style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr));">
    <div class="widget">
      <div class="widget-icon-wrap widget-icon-success">
        <i data-lucide="file-check"></i>
      </div>
      <p class="heading"><?= __('dashboard.published') ?></p>
      <p class="title"><?= (int) $postsCount ?></p>
      <div class="widget-footer">
        <a href="/admin?status=published"><?= __('dashboard.view_all') ?> &rarr;</a>
      </div>
    </div>

    <div class="widget">
      <div class="widget-icon-wrap widget-icon-warning">
        <i data-lucide="edit-3"></i>
      </div>
      <p class="heading"><?= __('dashboard.drafts') ?></p>
      <p class="title"><?= (int) $draftCount ?></p>
      <div class="widget-footer">
        <?php if ($draftCount > 0): ?>
          <a href="/admin?status=draft"><?= __('dashboard.review_drafts') ?> &rarr;</a>
        <?php else: ?>
          <span class="text-muted"><?= __('dashboard.no_drafts') ?></span>
        <?php endif; ?>
      </div>
    </div>

    <div class="widget">
      <div class="widget-icon-wrap widget-icon-primary">
        <i data-lucide="file"></i>
      </div>
      <p class="heading"><?= __('dashboard.pages') ?></p>
      <p class="title"><?= (int) ($pagesCount ?? 0) ?></p>
      <div class="widget-footer">
        <a href="/admin/pages"><?= __('nav.pages') ?> &rarr;</a>
      </div>
    </div>

    <div class="widget">
      <div class="widget-icon-wrap widget-icon-primary">
        <i data-lucide="image"></i>
      </div>
      <p class="heading"><?= __('dashboard.media_files') ?></p>
      <p class="title"><?= (int) ($mediaCount ?? 0) ?></p>
      <div class="widget-footer">
        <a href="/admin/media"><?= __('nav.media') ?> &rarr;</a>
      </div>
    </div>

    <div class="widget">
      <div class="widget-icon-wrap widget-icon-primary">
        <i data-lucide="archive"></i>
      </div>
      <p class="heading"><?= __('dashboard.backups_count') ?></p>
      <p class="title"><?= (int) ($backupsCount ?? 0) ?></p>
      <div class="widget-footer">
        <a href="/admin/backup"><?= __('nav.backups') ?> &rarr;</a>
      </div>
    </div>

    <div class="widget">
      <div class="widget-icon-wrap widget-icon-danger">
        <i data-lucide="trash-2"></i>
      </div>
      <p class="heading"><?= __('dashboard.trash_count') ?></p>
      <p class="title"><?= (int) $trashCount ?></p>
      <div class="widget-footer">
        <?php if ($trashCount > 0): ?>
          <a href="/admin/trash" class="has-text-danger"><?= __('dashboard.inspect_trash') ?> &rarr;</a>
        <?php else: ?>
          <span class="text-muted"><?= __('dashboard.trash_clean') ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- System Health Alert Banner -->
  <div class="card mb-4" style="padding: 1.25rem 1.5rem; background: var(--white); border-left: 4px solid <?= !empty($systemHealthy) ? 'var(--primary)' : 'var(--danger)' ?>;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.85rem;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: <?= !empty($systemHealthy) ? 'rgba(49, 92, 82, 0.12)' : 'rgba(239, 68, 68, 0.12)' ?>; display: flex; align-items: center; justify-content: center; color: <?= !empty($systemHealthy) ? 'var(--primary)' : 'var(--danger)' ?>;">
          <i data-lucide="<?= !empty($systemHealthy) ? 'shield-check' : 'alert-triangle' ?>"></i>
        </div>
        <div>
          <h4 style="margin: 0; font-size: 1rem;"><?= __('dashboard.system_health') ?></h4>
          <p class="text-muted" style="margin: 0; font-size: 0.85rem;">
            <?= !empty($systemHealthy) ? __('dashboard.healthy') : 'Warning: Some system paths require attention.' ?>
          </p>
        </div>
      </div>
      <a href="/admin/system" class="button button-sm button-light">
        <i data-lucide="cpu"></i> <?= __('nav.system') ?>
      </a>
    </div>
  </div>

  <!-- Quick Action Navigation Bar -->
  <div class="card mb-4" style="padding: 1rem 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
      <span style="font-weight: 700; color: var(--text-light); font-size: 0.9rem;"><?= __('dashboard.quick_actions') ?>:</span>
      <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
        <a href="/admin" class="button button-sm button-light"><i data-lucide="list"></i> <?= __('nav.posts') ?></a>
        <a href="/admin/pages" class="button button-sm button-light"><i data-lucide="file"></i> <?= __('nav.pages') ?></a>
        <a href="/admin/media" class="button button-sm button-light"><i data-lucide="image"></i> <?= __('nav.media') ?></a>
        <a href="/admin/menu" class="button button-sm button-light"><i data-lucide="menu"></i> <?= __('nav.menu') ?></a>
        <a href="/admin/themes" class="button button-sm button-light"><i data-lucide="palette"></i> <?= __('nav.themes') ?></a>
        <a href="/admin/backup" class="button button-sm button-light"><i data-lucide="archive"></i> <?= __('nav.backups') ?></a>
        <a href="/admin/help" class="button button-sm button-light"><i data-lucide="help-circle"></i> <?= __('nav.help') ?></a>
        <a href="/admin/settings" class="button button-sm button-light"><i data-lucide="settings"></i> <?= __('nav.settings') ?></a>
      </div>
    </div>
  </div>

  <!-- Two Column Layout: Pending Drafts & Audit Activity -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

    <!-- Pending Drafts Card -->
    <div class="card" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem;">
        <h3 style="margin: 0; font-size: 1.05rem; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="edit-3" style="color: var(--warning);"></i> <?= __('dashboard.drafts') ?>
        </h3>
        <a href="/admin?status=draft" class="button button-sm button-light"><?= __('common.all') ?> &rarr;</a>
      </div>

      <?php if (!empty($recentDrafts)): ?>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
          <?php foreach ($recentDrafts as $dId => $draft): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.65rem; background: var(--bg-soft); border-radius: 6px; gap: 0.5rem;">
              <div style="overflow: hidden;">
                <div style="font-weight: 600; font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  <?= htmlspecialchars($draft['title'] ?? 'Untitled Draft') ?>
                </div>
                <div class="text-muted" style="font-size: 0.78rem;">
                  <?= date('M j, Y', (int) ($draft['updated_at'] ?? $draft['created_at'] ?? time())) ?>
                </div>
              </div>
              <div class="flex gap-1" style="flex-shrink: 0;">
                <a href="/admin/edit/<?= (int) $dId ?>" class="button button-sm button-light" style="padding: 0.25rem 0.55rem; font-size: 0.8rem;">
                  <i data-lucide="edit-2"></i> <?= __('common.edit') ?>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center" style="padding: 2rem 1rem; color: var(--text-muted);">
          <i data-lucide="check-circle" style="width: 32px; height: 32px; color: var(--primary); margin: 0 auto 0.5rem;"></i>
          <p style="margin: 0; font-size: 0.9rem;"><?= __('dashboard.no_drafts') ?></p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Recent Audit Activity Card -->
    <div class="card" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem;">
        <h3 style="margin: 0; font-size: 1.05rem; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="activity" style="color: var(--primary);"></i> <?= __('dashboard.recent_activity') ?>
        </h3>
        <span class="badge" style="background: var(--bg-soft); font-size: 0.78rem;">Audit Trail</span>
      </div>

      <?php if (!empty($recentAudit)): ?>
        <div style="display: flex; flex-direction: column; gap: 0.65rem;">
          <?php foreach ($recentAudit as $audit): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; padding: 0.4rem 0.5rem; border-bottom: 1px solid var(--bg-soft); gap: 0.5rem;">
              <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden;">
                <code style="font-size: 0.78rem; background: var(--bg-soft); padding: 0.15rem 0.4rem; border-radius: 4px; color: var(--primary);">
                  <?= htmlspecialchars($audit['action'] ?? 'action') ?>
                </code>
                <span class="text-muted" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.8rem;">
                  by <strong><?= htmlspecialchars($audit['actor'] ?? 'admin') ?></strong>
                </span>
              </div>
              <span class="text-muted" style="font-size: 0.75rem; white-space: nowrap;">
                <?= isset($audit['timestamp']) ? date('M j, H:i', (int) $audit['timestamp']) : '' ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center" style="padding: 2rem 1rem; color: var(--text-muted);">
          <i data-lucide="shield" style="width: 32px; height: 32px; color: var(--text-muted); margin: 0 auto 0.5rem;"></i>
          <p style="margin: 0; font-size: 0.9rem;">Audit logging active. Events will appear here.</p>
        </div>
      <?php endif; ?>
    </div>

  </div>

  <!-- Recent Articles Header -->
  <div class="flex items-center justify-between mb-3">
    <h2><?= __('dashboard.recent_content') ?></h2>
    <a href="/admin" class="button button-sm button-light"><?= __('common.all') ?> &rarr;</a>
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
              <span class="badge badge-warning"><?= __('posts.status_draft') ?></span>
            <?php else: ?>
              <span class="badge badge-success"><?= __('posts.status_published') ?></span>
            <?php endif; ?>
          </div>

          <div class="excerpt">
            <?= htmlspecialchars(!empty($post['excerpt']) ? $post['excerpt'] : (mb_substr(strip_tags($post['content'] ?? ''), 0, 110) . '…')) ?>
          </div>

          <div class="actions">
            <a href="/admin/edit/<?= (int) $id ?>" class="button button-sm button-light">
              <i data-lucide="edit-2"></i> <?= __('common.edit') ?>
            </a>
            <a href="/post/<?= htmlspecialchars($post['slug'] ?? '') ?>" class="button button-sm button-light" target="_blank" rel="noopener">
              <i data-lucide="eye"></i> <?= __('common.preview') ?>
            </a>
            <form method="POST" action="/admin/delete/<?= (int) $id ?>" style="display:inline; margin-left: auto;">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="button button-sm button-light" style="color: var(--danger);" title="<?= __('posts.move_to_trash') ?>" onclick="return confirm('<?= __('common.confirm_delete') ?>')">
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
      <h3><?= __('dashboard.no_posts') ?></h3>
      <p class="text-muted" style="max-width: 420px; margin: 0 auto 1.5rem;"><?= __('dashboard.tagline') ?></p>
      <a href="/admin/add" class="button button-primary">
        <i data-lucide="plus-circle"></i> <?= __('dashboard.write_new') ?>
      </a>
    </div>
  <?php endif; ?>

</section>
