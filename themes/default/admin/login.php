<?php $this->layout('layouts/auth', ['title' => 'Sign In']) ?>

<div style="width: 100%;">
  <div class="text-center mb-4">
    <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 16px; margin-bottom: 1rem; box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);">
      <svg viewBox="0 0 40 40" width="64" height="64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="habrinLoginGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#6366f1" />
            <stop offset="100%" stop-color="#4338ca" />
          </linearGradient>
        </defs>
        <rect width="40" height="40" rx="10" fill="url(#habrinLoginGrad)" />
        <path d="M12 10V30M28 10V30M12 20H28" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="20" cy="20" r="2.5" fill="#ffffff" />
      </svg>
    </div>
    <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.35rem;">Sign in to Habrin</h1>
    <p class="text-muted" style="font-size: 0.92rem; margin: 0;">Enter your administrator credentials to continue</p>
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
