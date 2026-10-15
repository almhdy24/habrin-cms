<?php $this->layout('layouts/admin', ['title' => 'Content Backups']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-0"><i data-lucide="archive"></i> Content Backups</h2>
      <p class="text-muted" style="margin-top:0.25rem;font-size:0.9rem;">Export, download, and safely restore your CMS content and settings.</p>
    </div>
    <form method="POST" action="/admin/backup/create">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="button button-primary">
        <i data-lucide="download-cloud"></i> Create Backup Now
      </button>
    </form>
  </div>

  <!-- Restore Form Card -->
  <div class="card mb-4" style="padding:1.5rem;">
    <h3 class="mb-3" style="font-size:1.1rem;"><i data-lucide="upload-cloud"></i> Restore from Backup Archive</h3>
    <p class="text-muted" style="font-size:0.85rem;margin-bottom:1rem;">
      Upload a <code>.zip</code> or <code>.json</code> backup archive created by this CMS. An automatic safety snapshot of your current content will be created prior to restoring.
    </p>
    <form method="POST" action="/admin/backup/restore" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2" onsubmit="return confirm('Restore this backup? Current content will be backed up and then overwritten.')">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <input type="file" name="backup_file" accept=".zip,.json" required class="input" style="max-width:320px;padding:0.4rem;">
      <button type="submit" class="button button-danger">
        <i data-lucide="rotate-ccw"></i> Restore Backup
      </button>
    </form>
  </div>

  <!-- Backup List -->
  <?php if (empty($backups)): ?>
    <div class="card text-center" style="padding:3rem 1rem;">
      <i data-lucide="archive" style="width:48px;height:48px;color:var(--text-light);margin-bottom:1rem;"></i>
      <h3 class="mb-1">No backups available</h3>
      <p class="text-muted">Click "Create Backup Now" above to generate a complete snapshot of your site content.</p>
    </div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th>Archive Filename</th>
            <th>Type</th>
            <th>File Size</th>
            <th>Date Created</th>
            <th style="min-width:160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($backups as $b): ?>
            <?php
            $filename = (string) $b['filename'];
            $safeName = htmlspecialchars($filename, ENT_QUOTES, 'UTF-8');
            ?>
            <tr>
              <td><strong><?= $safeName ?></strong></td>
              <td><span class="badge" style="background:var(--bg-soft);border:1px solid var(--border);padding:0.2rem 0.5rem;font-size:0.75rem;border-radius:3px;"><?= strtoupper((string) $b['type']) ?></span></td>
              <td style="font-size:0.85rem;color:var(--text-light);"><?= htmlspecialchars((string) $b['size_formatted'], ENT_QUOTES, 'UTF-8') ?></td>
              <td style="font-size:0.85rem;color:var(--text-light);"><?= date('M j, Y g:i A', (int) $b['created_at']) ?></td>
              <td>
                <div class="flex flex-wrap gap-1">
                  <a href="/admin/backup/download/<?= rawurlencode($filename) ?>" class="button button-sm button-light" title="Download backup archive">
                    <i data-lucide="download"></i> Download
                  </a>
                  <form method="POST" action="/admin/backup/delete/<?= rawurlencode($filename) ?>" style="display:inline;" onsubmit="return confirm('Delete this backup archive permanently?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="button button-sm button-danger" title="Delete backup">
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
  <?php endif; ?>
</section>
