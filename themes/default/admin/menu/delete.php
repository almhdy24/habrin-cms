<?php $this->layout('layouts/admin', ['title' => 'Delete Menu Item']) ?>

<section>
  <div class="card">
    <h2 class="mb-4">Delete Menu Item</h2>
    <p>Are you sure you want to delete "<strong><?= htmlspecialchars($item['label']) ?></strong>"?</p>
    <p class="text-muted">This action cannot be undone.</p>
    <form method="POST" class="flex gap-1 mt-4">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <button type="submit" class="button button-danger"><i data-lucide="trash-2"></i> Yes, Delete</button>
      <a href="/admin/menu" class="button button-light">Cancel</a>
    </form>
  </div>
</section>
