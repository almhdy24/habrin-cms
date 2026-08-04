<?php $this->layout('layouts/admin', ['title' => 'Plugins']) ?>

<section>
  <h2 class="mb-4"><i data-lucide="puzzle"></i> Plugins</h2>

  <?php if (empty($plugins)): ?>
    <div class="notification notification-success">
      <i data-lucide="info"></i>
      <span>No plugins found. Add plugin folders to the <code>/plugins</code> directory.</span>
    </div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th style="min-width:150px;">Plugin</th>
            <th>Description</th>
            <th style="min-width:80px;">Version</th>
            <th style="min-width:100px;">Status</th>
            <th style="min-width:220px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($plugins as $name => $info): ?>
            <tr>
              <td><strong><?= htmlspecialchars($name) ?></strong></td>
              <td><?= htmlspecialchars($info['description'] ?? '') ?></td>
              <td><?= htmlspecialchars($info['version'] ?? '') ?></td>
              <td>
                <span class="badge <?= $info['enabled'] ? 'badge-success' : 'badge-danger' ?>">
                  <?= $info['enabled'] ? 'Enabled' : 'Disabled' ?>
                </span>
              </td>
              <td>
                <div class="flex gap-1 flex-wrap">
                  <?php if ($info['enabled'] && $info['has_settings']): ?>
                    <a href="/admin/plugins/settings/<?= htmlspecialchars($name) ?>" class="button button-sm button-light">
                      <i data-lucide="settings"></i> Settings
                    </a>
                  <?php elseif (!$info['enabled'] && $info['has_settings']): ?>
                    <span class="button button-sm button-light" style="opacity:0.6;cursor:not-allowed;">
                      <i data-lucide="settings"></i> Settings (disabled)
                    </span>
                  <?php endif; ?>
                  <form method="POST" action="/admin/plugins/toggle/<?= htmlspecialchars($name) ?>" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                    <button type="submit" class="button button-sm <?= $info['enabled'] ? 'button-danger' : 'button-success' ?>">
                      <?= $info['enabled'] ? 'Disable' : 'Enable' ?>
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
