<?php $this->layout('layouts/admin', ['title' => 'Toggle Plugin: ' . $plugin]) ?>

<section style="max-width: 600px; margin: 2rem auto;">
  <div class="card" style="padding: 2rem;">
    <h2 class="mb-3" style="display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="help-circle" style="color: var(--primary);"></i>
      <span>Confirm Plugin Toggle</span>
    </h2>

    <p class="mb-4">
      Are you sure you want to change the status of plugin <strong><?= htmlspecialchars($plugin, ENT_QUOTES, 'UTF-8') ?></strong>?
    </p>

    <form method="POST" action="/admin/plugins/toggle/<?= htmlspecialchars($plugin, ENT_QUOTES, 'UTF-8') ?>" class="flex gap-2 items-center">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="button button-primary">
        <i data-lucide="check"></i> Confirm Toggle
      </button>
      <a href="/admin/plugins" class="button button-light">Cancel</a>
    </form>
  </div>
</section>
