<?php $this->layout('layouts/admin', ['title' => 'System']) ?>

<section>

  <h2 class="mb-4"><i data-lucide="info"></i> System Information</h2>

  <div class="card mb-4">
    <h3 class="mb-2">CMS</h3>
    <ul style="list-style: none; padding: 0;">
      <li><strong>Name:</strong> <?= htmlspecialchars($system['cms']['name'] ?? 'Habrin CMS') ?></li>
      <li><strong>Version:</strong> <?= htmlspecialchars($system['cms']['version'] ?? '') ?></li>
      <li><strong>Storage:</strong> <?= htmlspecialchars($system['cms']['storage'] ?? 'Flat-file JSON (sharded)') ?></li>
    </ul>
  </div>

  <div class="card mb-4">
    <h3 class="mb-2">Environment</h3>
    <ul style="list-style: none; padding: 0;">
      <li><strong>PHP Version:</strong> <?= htmlspecialchars($system['env']['php_version'] ?? '') ?> (<?= htmlspecialchars($system['env']['php_sapi'] ?? '') ?>)</li>
      <li><strong>Operating System:</strong> <?= htmlspecialchars($system['env']['os'] ?? '') ?></li>
      <li><strong>Timezone:</strong> <?= htmlspecialchars($system['env']['timezone'] ?? '') ?></li>
      <li><strong>Memory Limit:</strong> <?= htmlspecialchars($system['env']['memory'] ?? '') ?></li>
      <li><strong>Max Execution Time:</strong> <?= htmlspecialchars(ini_get('max_execution_time')) ?> sec</li>
      <li><strong>Upload Max Filesize:</strong> <?= htmlspecialchars(ini_get('upload_max_filesize')) ?></li>
    </ul>
  </div>

  <div class="card mb-4">
    <h3 class="mb-2">Filesystem</h3>
    <ul style="list-style: none; padding: 0;">
      <li>Content writable: <?= $system['paths']['content_writable'] ? 'Yes' : 'No' ?></li>
      <li>Logs writable: <?= $system['paths']['logs_writable'] ? 'Yes' : 'No' ?></li>
      <li>Root writable: <?= $system['paths']['root_writable'] ? 'Yes' : 'No' ?></li>
    </ul>
  </div>

  <div class="card">
    <h3 class="mb-2">Plugins</h3>
    <?php if (empty($system['plugins'])): ?>
      <p>No plugins loaded.</p>
    <?php else: ?>
      <ul style="list-style: none; padding: 0;">
        <?php foreach ($system['plugins'] as $plugin): ?>
          <li><span class="badge badge-success">Enabled</span> <?= htmlspecialchars($plugin) ?></li>
        <?php endforeach ?>
      </ul>
    <?php endif ?>
  </div>

</section>

  <div class="card">
    <h3 class="mb-2">Hooks</h3>
    <?php
    $actions = \Core\Hooks::get_actions();
    $filters = \Core\Hooks::get_filters();
    $actionCount = 0;
    foreach ($actions as $hook) $actionCount += count($hook);
    $filterCount = 0;
    foreach ($filters as $hook) $filterCount += count($hook);
    ?>
    <p><strong>Actions:</strong> <?= $actionCount ?> registered on <?= count($actions) ?> hooks</p>
    <p><strong>Filters:</strong> <?= $filterCount ?> registered on <?= count($filters) ?> hooks</p>
    <?php if (!empty($actions)): ?>
      <details>
        <summary>View Actions</summary>
        <ul>
          <?php foreach ($actions as $hook => $callbacks): ?>
            <li><strong><?= htmlspecialchars($hook) ?></strong> (<?= count($callbacks) ?> callbacks)</li>
          <?php endforeach; ?>
        </ul>
      </details>
    <?php endif; ?>
  </div>
