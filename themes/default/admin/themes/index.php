<?php $this->layout('layouts/admin', ['title' => 'Themes']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-0"><i data-lucide="palette"></i> Themes</h2>
      <p class="text-muted" style="margin-top:0.25rem;font-size:0.9rem;">
        <?php if ($activeData): ?>
          Active: <strong><?= htmlspecialchars($activeData['name']) ?></strong> (v<?= htmlspecialchars($activeData['version']) ?>)
        <?php endif; ?>
      </p>
    </div>
    <div style="display:flex;gap:0.5rem;">
      <a href="/" target="_blank" class="button button-light button-sm">
        <i data-lucide="external-link"></i> Preview Site
      </a>
    </div>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="notification notification-danger">
      <i data-lucide="alert-circle"></i>
      <strong>Theme loading errors:</strong>
      <ul style="margin-top:0.5rem;padding-left:1.5rem;">
        <?php foreach ($errors as $theme => $error): ?>
          <li><strong><?= htmlspecialchars($theme) ?>:</strong> <?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (empty($themes)): ?>
    <div class="notification notification-danger">
      <i data-lucide="alert-circle"></i>
      <span>No themes found. Make sure themes are in the <code>/themes</code> directory with a valid <code>theme.json</code> manifest.</span>
    </div>
  <?php else: ?>
    <div class="themes-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;">
      <?php foreach ($themes as $name => $data): ?>
        <?php
        // Screenshot handling
        $screenshotUrl = null;
        if (!empty($data['screenshot'])) {
            $screenshotPath = '/themes/' . $name . '/' . $data['screenshot'];
            $fullPath = App::path('themes', $name, $data['screenshot']);
            if (is_file($fullPath)) {
                $screenshotUrl = $this->asset($screenshotPath);
            }
        }

        // Generate a clean SVG placeholder if no screenshot
        if (!$screenshotUrl) {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="200" viewBox="0 0 300 200">' .
                   '<rect width="300" height="200" fill="#f0f0f0"/>' .
                   '<text x="150" y="110" font-family="sans-serif" font-size="20" fill="#aaa" text-anchor="middle">' .
                   htmlspecialchars($data['name']) .
                   '</text></svg>';
            $screenshotUrl = 'data:image/svg+xml;charset=utf-8,' . rawurlencode($svg);
        }
        ?>
        <div class="theme-card" style="display:flex;flex-direction:column;background:white;border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);border:2px solid <?= $data['active'] ? 'var(--primary)' : 'transparent' ?>;transition:all 0.2s;position:relative;">
          <?php if ($data['active']): ?>
            <span style="position:absolute;top:0.5rem;right:0.5rem;background:var(--primary);color:white;padding:0.2rem 0.8rem;border-radius:var(--radius-full);font-size:0.7rem;font-weight:700;z-index:2;">Active</span>
          <?php endif; ?>

          <!-- Screenshot -->
          <div style="height:140px;background:#f0f0f0;overflow:hidden;display:flex;align-items:center;justify-content:center;">
            <img src="<?= $screenshotUrl ?>" alt="<?= htmlspecialchars($data['name']) ?>" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
          </div>

          <!-- Details -->
          <div style="padding:1rem 1.25rem;flex:1;display:flex;flex-direction:column;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
              <h3 style="margin:0;font-size:1.05rem;font-weight:700;"><?= htmlspecialchars($data['name']) ?></h3>
              <span style="font-size:0.7rem;color:var(--text-muted);background:var(--bg-soft);padding:0.1rem 0.5rem;border-radius:var(--radius);">v<?= htmlspecialchars($data['version']) ?></span>
            </div>

            <p style="font-size:0.85rem;color:var(--text-light);flex:1;margin:0 0 0.5rem 0;line-height:1.5;">
              <?= htmlspecialchars($data['description']) ?>
            </p>

            <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.75rem;">
              <?php if (!empty($data['author'])): ?>
                <span>By <?= htmlspecialchars($data['author']) ?></span>
              <?php endif; ?>
              <span> • <?= count($data['layouts']) ?> layouts</span>
            </div>

            <!-- Actions -->
            <div style="margin-top:auto;display:flex;gap:0.5rem;flex-wrap:wrap;">
              <?php if (!$data['active']): ?>
                <form method="POST" action="/admin/themes/activate/<?= htmlspecialchars($name) ?>" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                  <button type="submit" class="button button-primary button-sm">
                    <i data-lucide="check"></i> Activate
                  </button>
                </form>
              <?php else: ?>
                <span class="button button-sm" style="background:var(--bg-soft);color:var(--text-light);cursor:default;">
                  <i data-lucide="check-circle"></i> Active
                </span>
              <?php endif; ?>
              <a href="/?theme_preview=<?= htmlspecialchars($name) ?>" target="_blank" class="button button-light button-sm">
                <i data-lucide="eye"></i> Preview
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<style>
  .theme-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
  }
  .theme-card .button-sm {
    font-size: 0.75rem;
    padding: 0.3rem 0.6rem;
    min-height: 28px;
  }
  @media (max-width: 600px) {
    .themes-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
