<?php $this->layout('layouts/admin', ['title' => 'Media Library']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="image"></i> Media Library</h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;">
        Upload and manage images and media for your Habrin articles.
      </p>
    </div>
    <?php if (!empty($items)): ?>
      <span class="badge" style="background: var(--bg-soft); color: var(--text-muted); font-size: 0.85rem; padding: 0.35rem 0.75rem; border: 1px solid var(--border); border-radius: 999px;">
        <i data-lucide="folder" style="width: 14px; height: 14px; vertical-align: -2px;"></i>
        <strong><?= count($items) ?></strong> <?= count($items) === 1 ? 'file' : 'files' ?>
      </span>
    <?php endif; ?>
  </div>

  <!-- Upload Card & Dropzone -->
  <div class="card mb-4" style="padding: 1.75rem;">
    <h3 class="mb-2" style="font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="upload-cloud" style="color: var(--primary);"></i>
      <span>Upload Media</span>
    </h3>

    <form method="POST" action="/admin/media/upload" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2 mt-3">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <div style="flex: 1; min-width: 240px;">
        <label for="mediaFileInput" class="label" style="margin-bottom: 0.25rem;">Select file to upload</label>
        <input type="file" id="mediaFileInput" name="file" accept=".jpg,.jpeg,.png,.gif,.webp,.svg" required class="input" style="padding: 0.45rem;">
      </div>
      <button type="submit" class="button button-primary">
        <i data-lucide="upload"></i> Upload Asset
      </button>
    </form>
    <div class="help mt-2">
      <i data-lucide="info"></i>
      <span>Supports JPG, PNG, GIF, WEBP, and sanitized SVG. Maximum file size: 5 MB.</span>
    </div>
  </div>

  <!-- Media Items -->
  <?php if (empty($items)): ?>
    <div class="card text-center" style="padding: 3.5rem 1rem;">
      <i data-lucide="image" style="width: 52px; height: 52px; color: var(--text-muted); margin: 0 auto 1rem;"></i>
      <h3 class="mb-1">Your Media Library is Empty</h3>
      <p class="text-muted" style="max-width: 420px; margin: 0 auto 1.5rem;">Upload visual assets above to embed images into your Markdown posts and static pages.</p>
    </div>
  <?php else: ?>
    <!-- Search & Filter Bar -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
      <div style="flex: 1; max-width: 340px;">
        <input
          type="search"
          id="mediaSearchInput"
          class="input"
          placeholder="Filter media by filename..."
          style="padding: 0.45rem 0.75rem; font-size: 0.9rem;"
        >
      </div>
      <div id="mediaFilterCount" class="text-muted" style="font-size: 0.85rem;"></div>
    </div>

    <!-- Visual Cards Grid -->
    <div id="mediaGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap: 1.25rem;">
      <?php foreach ($items as $item): ?>
        <?php
        $name = (string) $item['name'];
        $url = (string) $item['url'];
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $mdSnippet = '![' . $safeName . '](' . $safeUrl . ')';
        ?>
        <div class="card media-card" data-name="<?= strtolower($safeName) ?>" style="padding: 1rem; display: flex; flex-direction: column; justify-content: space-between;">
          <div style="width: 100%; height: 160px; background: var(--bg-soft); border-radius: var(--radius); overflow: hidden; display: flex; align-items: center; justify-content: center; margin-bottom: 0.85rem; border: 1px solid var(--border);">
            <?php if (!empty($item['is_image'])): ?>
              <img src="<?= $safeUrl ?>" alt="<?= $safeName ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <i data-lucide="file" style="width: 48px; height: 48px; color: var(--text-muted);"></i>
            <?php endif; ?>
          </div>

          <div>
            <div style="font-weight: 700; font-size: 0.95rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 0.25rem;" title="<?= $safeName ?>">
              <?= $safeName ?>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-light); margin-bottom: 1rem;">
              <span><?= htmlspecialchars((string) $item['size_formatted'], ENT_QUOTES, 'UTF-8') ?></span>
              <span><?= date('M j, Y', (int) $item['created_at']) ?></span>
            </div>
          </div>

          <div style="display: flex; gap: 0.35rem; padding-top: 0.75rem; border-top: 1px solid var(--border); flex-wrap: wrap;">
            <button type="button" class="button button-sm button-light" style="flex: 1;" onclick="navigator.clipboard.writeText('<?= addslashes($mdSnippet) ?>').then(() => { if (window.habrinToast) window.habrinToast('Markdown code copied!'); else alert('Copied!'); })" title="Copy Markdown image code">
              <i data-lucide="copy"></i> MD
            </button>
            <button type="button" class="button button-sm button-light" onclick="navigator.clipboard.writeText('<?= addslashes($safeUrl) ?>').then(() => { if (window.habrinToast) window.habrinToast('Image URL copied!'); else alert('Copied URL!'); })" title="Copy raw URL">
              <i data-lucide="link"></i> URL
            </button>
            <a href="<?= $safeUrl ?>" target="_blank" rel="noopener" class="button button-sm button-light" title="View Full Image">
              <i data-lucide="external-link"></i>
            </a>
            <form method="POST" action="/admin/media/delete/<?= rawurlencode($name) ?>" onsubmit="return confirm('Permanently delete this file?')">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="button button-sm button-light" style="color: var(--danger);" title="Delete File">
                <i data-lucide="trash-2"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div id="mediaNoMatches" class="card text-center" style="display: none; padding: 2.5rem 1rem; margin-top: 1rem;">
      <i data-lucide="search" style="width: 36px; height: 36px; color: var(--text-muted); margin: 0 auto 0.75rem;"></i>
      <p class="text-muted" style="margin: 0;">No media files match your filter.</p>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('mediaSearchInput');
      const cards = document.querySelectorAll('.media-card');
      const noMatches = document.getElementById('mediaNoMatches');
      const countEl = document.getElementById('mediaFilterCount');

      if (!searchInput || !cards.length) return;

      searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        cards.forEach(card => {
          const name = card.getAttribute('data-name') || '';
          if (!query || name.includes(query)) {
            card.style.display = 'flex';
            visibleCount++;
          } else {
            card.style.display = 'none';
          }
        });

        if (noMatches) {
          noMatches.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        if (countEl) {
          countEl.textContent = query ? `Showing ${visibleCount} of ${cards.length}` : '';
        }
      });
    });
    </script>
  <?php endif; ?>
</section>
