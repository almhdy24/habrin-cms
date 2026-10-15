<?php $this->layout('layouts/installer', [
  'title' => 'Admin Provisioning',
  'step' => 'admin',
  'flash' => $flash ?? [],
]);
?>

<h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
  <i data-lucide="shield" style="color: var(--primary);"></i>
  <span>Administrator Account</span>
</h2>

<p class="text-muted" style="font-size: 0.9rem; margin-bottom: 1.5rem;">
  Configure your primary site details and secure your administrative login.
</p>

<form method="post">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

  <div class="field">
    <label class="label"><i data-lucide="globe"></i> Website Title</label>
    <input class="input" name="site_name" placeholder="e.g. My Habrin Journal" value="<?= htmlspecialchars((string) ($old['site_name'] ?? 'Habrin Journal'), ENT_QUOTES, 'UTF-8') ?>" required>
  </div>

  <div class="field">
    <label class="label"><i data-lucide="user"></i> Administrator Username</label>
    <input class="input" name="admin_user" placeholder="e.g. admin" value="<?= htmlspecialchars((string) ($old['admin_user'] ?? 'admin'), ENT_QUOTES, 'UTF-8') ?>" required>
  </div>

  <div class="field">
    <label class="label"><i data-lucide="lock"></i> Administrator Password</label>
    <input class="input" type="password" name="admin_pass" placeholder="At least 8 characters" required>
    <div class="help"><i data-lucide="info"></i> Passwords must be at least 8 characters long and avoid common defaults.</div>
  </div>

  <div class="field">
    <label class="label"><i data-lucide="check-circle"></i> Confirm Password</label>
    <input class="input" type="password" name="admin_pass_confirm" placeholder="Repeat your password" required>
  </div>

  <button type="submit" class="button button-primary button-block button-lg mt-4">
    <i data-lucide="shield-check"></i>
    <span>Complete Installation</span>
  </button>
</form>
