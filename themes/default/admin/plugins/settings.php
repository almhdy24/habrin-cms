<?php $this->layout('layouts/admin', ['title' => 'Plugin Settings']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-0"><i data-lucide="settings"></i> <?= htmlspecialchars($plugin) ?> Settings</h2>
      <p class="text-muted" style="margin-top:0.25rem;">Configure the <?= htmlspecialchars($plugin) ?> plugin to suit your needs.</p>
    </div>
    <a href="/admin/plugins" class="button button-light">
      <i data-lucide="arrow-left"></i> Back to Plugins
    </a>
  </div>

  <div class="card" style="background:white;border-radius:var(--radius);box-shadow:var(--shadow);padding:2rem;">
    <?php
    // Pass current values (from controller) and errors
    \Core\PluginSettings::renderForm($plugin, $settings, $errors ?? []);
    ?>
  </div>
</section>

<style>
  .plugin-settings-form .field.has-error .input,
  .plugin-settings-form .field.has-error .textarea,
  .plugin-settings-form .field.has-error select {
    border-color: var(--danger) !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
  }
  .plugin-settings-form .field .help {
    font-size: 0.85rem;
    color: var(--text-muted);
  }
  .plugin-settings-form .field.has-error .help {
    color: var(--danger);
  }
</style>
