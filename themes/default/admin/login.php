<?php $this->layout('layouts/auth', ['title' => 'Admin Login']) ?>

<div style="max-width: 380px; margin: 0 auto; width: 100%;">
  <h1 class="text-center" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1.5rem;">
    <i data-lucide="shield" style="color: var(--primary);"></i> Admin Login
  </h1>

  <?php $this->insert('partials/flash', ['flash' => $flash ?? []]) ?>

  <form method="POST" action="/admin/login" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

    <div class="field">
      <label class="label"><i data-lucide="user"></i> Username</label>
      <input
        class="input"
        type="text"
        name="username"
        placeholder="Enter your username"
        value="<?= htmlspecialchars($old['username'] ?? '') ?>"
        required
        autofocus
      >
    </div>

    <div class="field">
      <label class="label"><i data-lucide="lock"></i> Password</label>
      <input
        class="input"
        type="password"
        name="password"
        placeholder="Enter your password"
        required
      >
    </div>

    <button type="submit" class="button button-primary button-block">
      <i data-lucide="log-in"></i> Login
    </button>
  </form>
</div>
