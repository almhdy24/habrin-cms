<?php $this->layout('layouts/admin', ['title' => 'System Status']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="info"></i> System Diagnostics &amp; Status</h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;">Overview of runtime environment, storage permissions, and active extensions.</p>
    </div>
    <a href="/admin/dashboard" class="button button-light">
      <i data-lucide="arrow-left"></i> Dashboard
    </a>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap: 1.5rem; align-items: start;">

    <!-- CMS Identity -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="feather" style="color: var(--primary);"></i> CMS Architecture
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
          <tr>
            <td class="text-muted" style="padding: 0.5rem 0;">Base Directory</td>
            <td style="text-align: right; font-size: 0.78rem; font-family: var(--font-mono); color: var(--text-muted); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars(BASE_PATH) ?>"><?= htmlspecialchars(basename(BASE_PATH)) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PHP Runtime Environment -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="server" style="color: var(--primary);"></i> Server Runtime
      </h3>
      <table style="width: 100%; font-size: 0.9rem; border-collapse: collapse;">
        <tbody>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">PHP Version</td>
            <td style="text-align: right; font-weight: 600; font-family: var(--font-mono);"><?= htmlspecialchars($system['env']['php_version'] ?? PHP_VERSION) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">SAPI</td>
            <td style="text-align: right;"><?= htmlspecialchars($system['env']['php_sapi'] ?? PHP_SAPI) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">Operating System</td>
            <td style="text-align: right;"><?= htmlspecialchars($system['env']['os'] ?? PHP_OS_FAMILY) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">Timezone</td>
            <td style="text-align: right; font-size: 0.85rem;"><?= htmlspecialchars($system['env']['timezone'] ?? date_default_timezone_get()) ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td class="text-muted" style="padding: 0.5rem 0;">Memory Limit</td>
            <td style="text-align: right; font-family: var(--font-mono); font-size: 0.85rem;"><?= htmlspecialchars($system['env']['memory'] ?? ini_get('memory_limit')) ?></td>
          </tr>
          <tr>
            <td class="text-muted" style="padding: 0.5rem 0;">Upload Max File Size</td>
            <td style="text-align: right; font-family: var(--font-mono); font-size: 0.85rem;"><?= htmlspecialchars(ini_get('upload_max_filesize')) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Storage & Permissions -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="hard-drive" style="color: var(--primary);"></i> Filesystem &amp; Permissions
      </h3>
      <table style="width: 100%; font-size: 0.9rem; border-collapse: collapse;">
        <tbody>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td style="padding: 0.5rem 0;"><code>content/</code> Storage</td>
            <td style="text-align: right;">
              <?php if (!empty($system['paths']['content_writable'])): ?>
                <span class="badge" style="background: rgba(49, 92, 82, 0.12); color: var(--primary); font-size: 0.78rem;">🟢 Writable</span>
              <?php else: ?>
                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: var(--danger); font-size: 0.78rem;">🔴 Read-Only</span>
              <?php endif; ?>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid var(--bg-soft);">
            <td style="padding: 0.5rem 0;"><code>logs/</code> Audit Records</td>
            <td style="text-align: right;">
              <?php if (!empty($system['paths']['logs_writable'])): ?>
                <span class="badge" style="background: rgba(49, 92, 82, 0.12); color: var(--primary); font-size: 0.78rem;">🟢 Writable</span>
              <?php else: ?>
                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: var(--danger); font-size: 0.78rem;">🔴 Read-Only</span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td style="padding: 0.5rem 0;">Application Root</td>
            <td style="text-align: right;">
              <?php if (!empty($system['paths']['root_writable'])): ?>
                <span class="badge" style="background: rgba(49, 92, 82, 0.12); color: var(--primary); font-size: 0.78rem;">🟢 Writable</span>
              <?php else: ?>
                <span class="badge" style="background: var(--bg-soft); color: var(--text-muted); font-size: 0.78rem;">⚪ Locked</span>
              <?php endif; ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Extension Plugins & Lifecycle Hooks -->
    <div class="card" style="padding: 1.5rem;">
      <h3 style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.6rem; margin-bottom: 1rem;">
        <i data-lucide="plug" style="color: var(--primary);"></i> Extensions &amp; Hooks
      </h3>
      <div class="mb-3">
        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-muted);">ACTIVE PLUGINS</div>
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
        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-muted);">LIFECYCLE PIPELINE</div>
        <p style="font-size: 0.85rem; margin: 0 0 0.5rem 0;">
          <strong><?= $actionCount ?></strong> action callbacks across <strong><?= count($actions) ?></strong> hooks ·
          <strong><?= $filterCount ?></strong> filter callbacks
        </p>
        <?php if (!empty($actions)): ?>
          <details style="font-size: 0.82rem; color: var(--text-muted);">
            <summary style="cursor: pointer; font-weight: 600;">Inspect Registered Action Hooks</summary>
            <ul style="margin: 0.5rem 0 0 1rem; padding: 0; line-height: 1.6;">
              <?php foreach ($actions as $hook => $callbacks): ?>
                <li><code><?= htmlspecialchars($hook) ?></code> (<?= count($callbacks) ?>)</li>
              <?php endforeach; ?>
            </ul>
          </details>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>
