<?php $this->layout('layouts/admin', ['title' => 'Media Library']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-0"><i data-lucide="image"></i> Media Library</h2>
      <p class="text-muted" style="margin-top:0.25rem;font-size:0.9rem;">Upload images and files for your posts and pages.</p>
    </div>
  </div>

  <!-- Upload Card -->
  <div class="card mb-4" style="padding:1.5rem;">
    <h3 class="mb-3" style="font-size:1.1rem;"><i data-lucide="upload-cloud"></i> Upload New File</h3>
    <form method="POST" action="/admin/media/upload" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <input type="file" name="file" accept=".jpg,.jpeg,.png,.gif,.webp,.svg" required class="input" style="max-width:320px;padding:0.4rem;">
      <button type="submit" class="button button-primary">
        <i data-lucide="upload"></i> Upload
      </button>
      <span class="text-muted" style="font-size:0.85rem;">Max 5 MB. Allowed: JPG, PNG, GIF, WEBP, SVG</span>
    </form>
  </div>

  <!-- Media Grid -->
  <?php if (empty($items)): ?>
    <div class="card text-center" style="padding:3rem 1rem;">
      <i data-lucide="image" style="width:48px;height:48px;color:var(--text-light);margin-bottom:1rem;"></i>
      <h3 class="mb-1">No media files found</h3>
      <p class="text-muted">Upload your first image above to embed in posts or pages.</p>
    </div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th style="width:80px;">Preview</th>
            <th>Filename</th>
            <th>Size</th>
            <th>Date</th>
            <th style="min-width:180px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <?php
            $name = (string) $item['name'];
            $url = (string) $item['url'];
            $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $mdSnippet = '![' . $safeName . '](' . $safeUrl . ')';
            ?>
            <tr>
              <td>
                <?php if (!empty($item['is_image'])): ?>
                  <a href="<?= $safeUrl ?>" target="_blank" rel="noopener">
                    <img src="<?= $safeUrl ?>" alt="<?= $safeName ?>" style="width:60px;height:45px;object-fit:cover;border-radius:4px;border:1px solid var(--border);">
                  </a>
                <?php else: ?>
                  <i data-lucide="file" style="width:32px;height:32px;color:var(--text-light);"></i>
                <?php endif; ?>
              </td>
              <td>
                <strong><?= $safeName ?></strong>
                <div style="font-size:0.8rem;color:var(--text-light);font-family:monospace;"><?= $safeUrl ?></div>
              </td>
              <td style="font-size:0.85rem;color:var(--text-light);"><?= htmlspecialchars((string) $item['size_formatted'], ENT_QUOTES, 'UTF-8') ?></td>
              <td style="font-size:0.85rem;color:var(--text-light);"><?= date('M j, Y g:i A', (int) $item['created_at']) ?></td>
              <td>
                <div class="flex flex-wrap gap-1">
                  <button type="button" class="button button-sm button-light" onclick="navigator.clipboard.writeText('<?= addslashes($mdSnippet) ?>').then(() => alert('Markdown copied to clipboard!'))" title="Copy Markdown code">
                    <i data-lucide="copy"></i> Copy MD
                  </button>
                  <a href="<?= $safeUrl ?>" target="_blank" rel="noopener" class="button button-sm button-light" title="View file">
                    <i data-lucide="external-link"></i>
                  </a>
                  <form method="POST" action="/admin/media/delete/<?= rawurlencode($name) ?>" style="display:inline;" onsubmit="return confirm('Permanently delete this file?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="button button-sm button-danger" title="Delete file">
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
