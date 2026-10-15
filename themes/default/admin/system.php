<?php $this->layout('layouts/admin', ['title' => __('system.title')]) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="info"></i> <?= __('system.title') ?></h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;"><?= __('system.overview') ?></p>
    </div>
    <a href="/admin/dashboard" class="button button-light">
      <i data-lucide="arrow-left"></i> <?= __('nav.dashboard') ?>
    </a>
  </div>

  <?php if (!empty($system['warnings'])): ?>
    <div class="notification notification-warning mb-4" style="background: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b; padding: 1rem 1.25rem; border-radius: 6px;">
      <div style="font-weight: 700; color: #b45309; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="alert-triangle"></i> System Configuration Warnings
      </div>
      <ul style="margin: 0; padding-left: 1.25rem; color: #78350f; font-size: 0.88rem;">
        <?php foreach ($system['warnings'] as $warning): ?>
          <li><?= htmlspecialchars($warning, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap: 1.5rem; align-items: start;">

    <!-- CMS Architecture -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="feather" style="color: var(--primary);"></i> <?= __('system.cms_arch') ?>
      </h3>
      <table style="width: 100%; font-size: 0.9rem; border-collapse: collapse;">
        <tbody>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">Name</td>
            <td style="text-align: right; font-weight: 600;"><?= htmlspecialchars($system['cms']['name'] ?? 'Habrin CMS') ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">Version</td>
            <td style="text-align: right;"><span class="badge" style="background: var(--bg-soft); font-family: var(--font-mono); font-size: 0.8rem;"><?= htmlspecialchars($system['cms']['version'] ?? '') ?></span></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">Storage Driver</td>
            <td style="text-align: right; font-size: 0.85rem;"><?= htmlspecialchars($system['cms']['storage'] ?? 'Flat-file JSON (sharded)') ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">Active Theme</td>
            <td style="text-align: right; font-weight: 600;"><?= htmlspecialchars($system['cms']['theme'] ?? 'default') ?></td>
          </tr>
          <tr>
            <td class="text-muted" style="padding: 0.5rem 0;">Base Directory</td>
            <td style="text-align: right; font-size: 0.78rem; font-family: var(--font-mono); color: var(--text-muted); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars(BASE_PATH) ?>"><?= htmlspecialchars(basename(BASE_PATH)) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Server Runtime Environment -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="server" style="color: var(--primary);"></i> <?= __('system.server_runtime') ?>
      </h3>
      <table style="width: 100%; font-size: 0.9rem; border-collapse: collapse;">
        <tbody>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;"><?= __('system.php_version') ?></td>
            <td style="text-align: right; font-weight: 600; font-family: var(--font-mono);"><?= htmlspecialchars($system['env']['php_version'] ?? PHP_VERSION) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">SAPI</td>
            <td style="text-align: right;"><?= htmlspecialchars($system['env']['php_sapi'] ?? PHP_SAPI) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;"><?= __('system.os') ?></td>
            <td style="text-align: right;"><?= htmlspecialchars($system['env']['os'] ?? PHP_OS_FAMILY) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;"><?= __('system.timezone') ?></td>
            <td style="text-align: right; font-size: 0.85rem;"><?= htmlspecialchars($system['env']['timezone'] ?? date_default_timezone_get()) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;"><?= __('system.memory_limit') ?></td>
            <td style="text-align: right; font-family: var(--font-mono); font-size: 0.85rem;"><?= htmlspecialchars($system['env']['memory'] ?? ini_get('memory_limit')) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;"><?= __('system.upload_max') ?></td>
            <td style="text-align: right; font-family: var(--font-mono); font-size: 0.85rem;"><?= htmlspecialchars($system['env']['upload_max'] ?? ini_get('upload_max_filesize')) ?></td>
          </tr>
          <tr>
            <td class="text-muted" style="padding: 0.5rem 0;">Post Max Size</td>
            <td style="text-align: right; font-family: var(--font-mono); font-size: 0.85rem;"><?= htmlspecialchars($system['env']['post_max'] ?? ini_get('post_max_size')) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Filesystem, Storage & Permissions -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="hard-drive" style="color: var(--primary);"></i> <?= __('system.filesystem') ?>
      </h3>
      <table style="width: 100%; font-size: 0.9rem; border-collapse: collapse;">
        <tbody>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td style="padding: 0.5rem 0;"><code>content/</code> (<?= htmlspecialchars($system['storage']['content_size'] ?? '0 B') ?>)</td>
            <td style="text-align: right;">
              <?php if (!empty($system['paths']['content_writable'])): ?>
                <span class="badge" style="background: rgba(49, 92, 82, 0.12); color: var(--primary); font-size: 0.78rem;">🟢 <?= __('system.writable') ?></span>
              <?php else: ?>
                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: var(--danger); font-size: 0.78rem;">🔴 <?= __('system.readonly') ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td style="padding: 0.5rem 0;"><code>uploads/</code> (<?= htmlspecialchars($system['storage']['uploads_size'] ?? '0 B') ?>)</td>
            <td style="text-align: right;">
              <?php if (!empty($system['paths']['uploads_writable'])): ?>
                <span class="badge" style="background: rgba(49, 92, 82, 0.12); color: var(--primary); font-size: 0.78rem;">🟢 <?= __('system.writable') ?></span>
              <?php else: ?>
                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: var(--danger); font-size: 0.78rem;">🔴 <?= __('system.readonly') ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td style="padding: 0.5rem 0;"><code>backups/</code> (<?= htmlspecialchars($system['storage']['backups_size'] ?? '0 B') ?>)</td>
            <td style="text-align: right;">
              <?php if (!empty($system['paths']['backups_writable'])): ?>
                <span class="badge" style="background: rgba(49, 92, 82, 0.12); color: var(--primary); font-size: 0.78rem;">🟢 <?= __('system.writable') ?></span>
              <?php else: ?>
                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: var(--danger); font-size: 0.78rem;">🔴 <?= __('system.readonly') ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td style="padding: 0.5rem 0;"><code>logs/</code> Audit Records</td>
            <td style="text-align: right;">
              <?php if (!empty($system['paths']['logs_writable'])): ?>
                <span class="badge" style="background: rgba(49, 92, 82, 0.12); color: var(--primary); font-size: 0.78rem;">🟢 <?= __('system.writable') ?></span>
              <?php else: ?>
                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: var(--danger); font-size: 0.78rem;">🔴 <?= __('system.readonly') ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td style="padding: 0.5rem 0;">Application Root</td>
            <td style="text-align: right;">
              <?php if (!empty($system['paths']['root_writable'])): ?>
                <span class="badge" style="background: rgba(49, 92, 82, 0.12); color: var(--primary); font-size: 0.78rem;">🟢 <?= __('system.writable') ?></span>
              <?php else: ?>
                <span class="badge" style="background: var(--bg-soft); color: var(--text-muted); font-size: 0.78rem;">⚪ Locked</span>
              <?php endif; ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PHP Extensions -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="layers" style="color: var(--primary);"></i> <?= __('system.extensions') ?>
      </h3>
      <div class="mb-3">
        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-muted);">REQUIRED EXTENSIONS</div>
        <div class="flex flex-wrap gap-1">
          <?php foreach (($system['extensions']['required'] ?? []) as $ext => $loaded): ?>
            <span class="badge" style="background: <?= $loaded ? 'rgba(49, 92, 82, 0.12)' : 'rgba(239, 68, 68, 0.12)' ?>; color: <?= $loaded ? 'var(--primary)' : 'var(--danger)' ?>; font-size: 0.82rem; padding: 0.25rem 0.5rem;">
              <i data-lucide="<?= $loaded ? 'check' : 'x' ?>" style="width: 12px; height: 12px; vertical-align: -1px;"></i>
              <?= htmlspecialchars($ext) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>

      <div>
        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-muted);">RECOMMENDED / OPTIONAL</div>
        <div class="flex flex-wrap gap-1">
          <?php foreach (($system['extensions']['optional'] ?? []) as $ext => $loaded): ?>
            <span class="badge" style="background: <?= $loaded ? 'var(--bg-soft)' : 'transparent' ?>; border: 1px solid var(--border); color: <?= $loaded ? 'var(--text)' : 'var(--text-muted)' ?>; font-size: 0.82rem; padding: 0.25rem 0.5rem;">
              <i data-lucide="<?= $loaded ? 'check' : 'minus' ?>" style="width: 12px; height: 12px; color: <?= $loaded ? 'var(--primary)' : 'var(--text-muted)' ?>; vertical-align: -1px;"></i>
              <?= htmlspecialchars($ext) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Active Plugins & Hooks -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="plug" style="color: var(--primary);"></i> <?= __('system.active_plugins') ?>
      </h3>
      <div class="mb-3">
        <?php if (empty($system['plugins'])): ?>
          <p class="text-muted" style="font-size: 0.85rem; margin: 0;">No active plugins loaded.</p>
        <?php else: ?>
          <div class="flex flex-wrap gap-1">
            <?php foreach ($system['plugins'] as $plugin): ?>
              <span class="badge" style="background: var(--bg-soft); border: 1px solid var(--border); font-size: 0.82rem; padding: 0.25rem 0.5rem;">
                <i data-lucide="check" style="width: 12px; height: 12px; color: var(--primary); vertical-align: -1px;"></i>
                <?= htmlspecialchars($plugin) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div>
        <?php
        $actions = \Core\Hooks::get_actions();
        $filters = \Core\Hooks::get_filters();
        $actionCount = 0;
        foreach ($actions as $hook) $actionCount += count($hook);
        $filterCount = 0;
        foreach ($filters as $hook) $filterCount += count($hook);
        ?>
        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-muted);"><?= __('system.lifecycle_hooks') ?></div>
        <p style="font-size: 0.85rem; margin: 0 0 0.5rem 0;">
          <strong><?= $actionCount ?></strong> action listeners registered across <strong><?= count($actions) ?></strong> hook events.<br>
          <strong><?= $filterCount ?></strong> content filters active across <strong><?= count($filters) ?></strong> filter points.
        </p>
      </div>
    </div>

  </div>
</section>
