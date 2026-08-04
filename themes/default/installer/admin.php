<?php $this->layout('layouts/installer', [
  'title' => 'Install – Admin Setup',
  'step' => 'admin',
  'flash' => $flash ?? [],
]);
?>


<h2 class="title">Admin Setup</h2>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
  <div class="field">
    <label class="label">Site Name</label>
    <input class="input" name="site_name" required>
  </div>

  <div class="field">
    <label class="label">Admin Username</label>
    <input class="input" name="admin_user" required>
  </div>

  <div class="field">
    <label class="label">Password</label>
    <input class="input" type="password" name="admin_pass" required>
  </div>

  <div class="field">
    <label class="label">Confirm Password</label>
    <input class="input" type="password" name="admin_pass_confirm" required>
  </div>

  <button class="button is-fullwidth">
    <i data-lucide="shield-check"></i>
    Install
  </button>
</form>
