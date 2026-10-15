<?php $this->layout('layouts/admin', ['title' => 'Backups']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="archive"></i> Content Backups</h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;">
        Export, download, and safely restore your Habrin content and settings.
      </p>
    </div>
    <form method="POST" action="/admin/backup/create">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="button button-primary">
        <i data-lucide="download-cloud"></i> Create Backup Snapshot
      </button>
    </form>
  </div>

  <!-- Restore Form Card -->
  <div class="card mb-4" style="padding: 1.75rem;">
    <h3 class="mb-2" style="font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="upload-cloud" style="color: var(--primary);"></i>
      <span>Restore from Archive</span>
    </h3>
    <p class="text-muted" style="font-size: 0.88rem; margin-bottom: 1.25rem;">
      Upload a <code>.zip</code> or <code>.json</code> backup archive. An automated safety snapshot of your active content will be taken before restoration begins.
    </p>
    <form method="POST" action="/admin/backup/restore" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2" onsubmit="return confirm('Restore this backup? Current content will be automatically backed up first, then replaced.')">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <div style="flex: 1; min-width: 240px;">
        <label for="backupFileInput" class="label" style="margin-bottom: 0.25rem;">Select backup archive</label>
        <input type="file" id="backupFileInput" name="backup_file" accept=".zip,.json" required class="input" style="padding: 0.45rem;">
      </div>
      <button type="submit" class="button button-danger">
        <i data-lucide="rotate-ccw"></i> Restore Archive
      </button>
    </form>
  </div>

  <!-- Backup List -->
  <?php if (empty($backups)): ?>
    <div class="card text-center" style="padding: 3.5rem 1rem;">
      <i data-lucide="archive" style="width: 52px; height: 52px; color: var(--text-muted); margin: 0 auto 1rem;"></i>
      <h3 class="mb-1">No backups found</h3>
      <p class="text-muted" style="max-width: 380px; margin: 0 auto 1.5rem;">Click "Create Backup Snapshot" above to generate a downloadable archive of your content.</p>
    </div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th>Archive Filename</th>
            <th style="width: 100px;">Format</th>
            <th style="width: 120px;">Size</th>
            <th style="width: 160px;">Date Created</th>
            <th style="width: 160px; text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($backups as $b): ?>
            <?php
            $filename = (string) $b['filename'];
            $safeName = htmlspecialchars($filename, ENT_QUOTES, 'UTF-8');
            ?>
            <tr>
              <td>
                <span style="font-family: var(--font-mono); font-size: 0.9rem; font-weight: 600;"><?= $safeName ?></span>
              </td>
              <td><span class="badge badge-primary"><?= strtoupper((string) $b['type']) ?></span></td>
              <td style="font-size: 0.88rem; color: var(--text-light);"><?= htmlspecialchars((string) $b['size_formatted'], ENT_QUOTES, 'UTF-8') ?></td>
              <td style="font-size: 0.88rem; color: var(--text-light);"><?= date('M j, Y g:i A', (int) $b['created_at']) ?></td>
              <td style="text-align: right;">
                <div class="flex gap-1" style="justify-content: flex-end;">
                  <a href="/admin/backup/download/<?= rawurlencode($filename) ?>" class="button button-sm button-light" title="Download backup archive">
                    <i data-lucide="download"></i> Download
                  </a>
                  <form method="POST" action="/admin/backup/delete/<?= rawurlencode($filename) ?>" style="display: inline;" onsubmit="return confirm('Delete this backup archive permanently?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="button button-sm button-light" style="color: var(--danger);" title="Delete backup">
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
