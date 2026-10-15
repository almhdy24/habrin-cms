<?php $this->layout('layouts/installer', [
  'title' => 'System Verification',
  'step' => 'system',
  'flash' => $flash ?? [],
]) ?>

<h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
  <i data-lucide="check-square" style="color: var(--primary);"></i>
  <span>System Requirements</span>
</h2>

<p class="text-muted" style="font-size: 0.9rem; margin-bottom: 1.25rem;">
  Habrin CMS requires PHP 8.1+ and write permissions for local content storage.
</p>

<div class="card mb-4" style="background: var(--bg-soft); border-radius: var(--radius); padding: 1rem 1.25rem;">
  <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.75rem;">
    <li style="display: flex; align-items: center; justify-content: space-between;">
      <span style="font-size: 0.92rem; font-weight: 500;">PHP Version (&ge; 8.1.0)</span>
      <?php if (!empty($checks['php'])): ?>
        <span class="badge badge-success"><i data-lucide="check" style="width:14px;height:14px;"></i> <?= PHP_VERSION ?></span>
      <?php else: ?>
        <span class="badge badge-danger"><i data-lucide="x" style="width:14px;height:14px;"></i> <?= PHP_VERSION ?> Required &ge; 8.1</span>
      <?php endif; ?>
    </li>

    <li style="display: flex; align-items: center; justify-content: space-between;">
      <span style="font-size: 0.92rem; font-weight: 500;">JSON Extension</span>
      <?php if (!empty($checks['json'])): ?>
        <span class="badge badge-success"><i data-lucide="check" style="width:14px;height:14px;"></i> Available</span>
      <?php else: ?>
        <span class="badge badge-danger"><i data-lucide="x" style="width:14px;height:14px;"></i> Missing</span>
      <?php endif; ?>
    </li>

    <li style="display: flex; align-items: center; justify-content: space-between;">
      <span style="font-size: 0.92rem; font-weight: 500;">Content Storage Directory</span>
      <?php if (!empty($checks['content'])): ?>
        <span class="badge badge-success"><i data-lucide="check" style="width:14px;height:14px;"></i> Writable</span>
      <?php else: ?>
        <span class="badge badge-danger"><i data-lucide="x" style="width:14px;height:14px;"></i> Not Writable</span>
      <?php endif; ?>
    </li>

    <li style="display: flex; align-items: center; justify-content: space-between;">
      <span style="font-size: 0.92rem; font-weight: 500;">Application Root Permissions</span>
      <?php if (!empty($checks['root'])): ?>
        <span class="badge badge-success"><i data-lucide="check" style="width:14px;height:14px;"></i> Ready</span>
      <?php else: ?>
        <span class="badge badge-danger"><i data-lucide="x" style="width:14px;height:14px;"></i> Permission Issue</span>
      <?php endif; ?>
    </li>
  </ul>
</div>

<form method="post">
  <button type="submit" class="button button-primary button-block button-lg">
    <span>Continue to Admin Setup</span>
    <i data-lucide="arrow-right"></i>
  </button>
</form>
