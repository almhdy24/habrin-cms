<?php $this->layout('layouts/admin', ['title' => 'Activate Theme']) ?>

<section>
  <div class="card">
    <h2 class="mb-4"><i data-lucide="alert-circle"></i> Activate Theme</h2>
    <p>Are you sure you want to activate "<strong><?= htmlspecialchars($themeData['name']) ?></strong>"?</p>
    <p class="text-muted">This will change your site's appearance immediately.</p>
    <form method="POST" class="flex gap-1 mt-4">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <button type="submit" class="button button-primary"><i data-lucide="check"></i> Activate</button>
      <a href="/admin/themes" class="button button-light">Cancel</a>
    </form>
  </div>
</section>
