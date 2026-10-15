<?php $this->layout('layouts/auth', ['title' => 'Sign In']) ?>

<div style="width: 100%;">
  <div class="text-center mb-4">
    <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 12px; margin-bottom: 1rem;">
      <svg viewBox="0 0 40 40" width="64" height="64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="40" height="40" rx="8" fill="#315C52" />
        <rect x="0.5" y="0.5" width="39" height="39" rx="7.5" stroke="#274A42" stroke-width="1" />
        <path d="M12 11V29" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M28 11V29" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M12 20H28" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        <rect x="18.5" y="18.5" width="3" height="3" fill="#FFFFFF" rx="0.5" />
      </svg>
    </div>
    <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.35rem; color: var(--ink);">Sign in to Habrin</h1>
    <p class="text-muted" style="font-size: 0.92rem; margin: 0;">Publish without the clutter.</p>
  </div>

  <?php $this->insert('partials/flash', ['flash' => $flash ?? []]) ?>

  <form method="POST" action="/admin/login" class="card" style="padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <div class="field">
      <label class="label" for="loginUsername"><i data-lucide="user"></i> Username</label>
      <input
        id="loginUsername"
        class="input"
        type="text"
        name="username"
        placeholder="e.g. admin"
        value="<?= htmlspecialchars($old['username'] ?? '') ?>"
        required
        autofocus
      >
    </div>

    <div class="field">
      <label class="label" for="loginPassword"><i data-lucide="lock"></i> Password</label>
      <input
        id="loginPassword"
        class="input"
        type="password"
        name="password"
        placeholder="••••••••••••"
        required
      >
    </div>

    <button type="submit" class="button button-primary button-block button-lg mt-3" style="font-size: 1rem;">
      <i data-lucide="log-in"></i> Sign In to Console
    </button>
  </form>

  <div class="text-center mt-4">
    <a href="/" class="text-muted" style="font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.35rem;">
      <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Return to Website
    </a>
  </div>
</div>
