<?php $this->layout('layouts/installer', ['title' => 'Installation Complete', 'step' => 'finish']) ?>

<div class="text-center" style="padding: 1.5rem 0;">
  <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: #dcfce7; color: #16a34a; margin-bottom: 1.25rem;">
    <i data-lucide="check" style="width: 32px; height: 32px; stroke-width: 3;"></i>
  </div>

  <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Habrin CMS is Ready!</h2>

  <p class="text-muted" style="margin-bottom: 2rem;">
    Your administrator account is configured, sensitive files are secured, and the installer is permanently locked.
  </p>

  <div style="display: flex; flex-direction: column; gap: 0.75rem;">
    <a class="button button-primary button-block button-lg" href="/admin/login">
      <i data-lucide="log-in"></i> Sign in to Admin Panel
    </a>

    <a class="button button-light button-block" href="/">
      <i data-lucide="external-link"></i> Visit Homepage
    </a>
  </div>
</div>