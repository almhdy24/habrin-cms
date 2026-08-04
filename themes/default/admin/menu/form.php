<?php $this->layout('layouts/admin', ['title' => $item ? 'Edit Menu Item' : 'Add Menu Item']) ?>

<section>
  <h2 class="mb-4"><?= $item ? 'Edit Menu Item' : 'Add Menu Item' ?></h2>

  <form method="POST" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

    <div class="field">
      <label class="label"><i data-lucide="type"></i> Label</label>
      <input class="input" type="text" name="label" value="<?= htmlspecialchars($item['label'] ?? '') ?>" required>
    </div>

    <div class="field">
      <label class="label"><i data-lucide="link"></i> URL</label>
      <input class="input" type="text" name="url" value="<?= htmlspecialchars($item['url'] ?? '') ?>" placeholder="/about" required>
    </div>

    <div class="field">
      <label class="label"><i data-lucide="list"></i> Order</label>
      <input class="input" type="number" name="order" value="<?= (int) ($item['order'] ?? 0) ?>" min="0">
    </div>

    <div class="flex gap-1">
      <button type="submit" class="button button-primary"><i data-lucide="save"></i> Save</button>
      <a href="/admin/menu" class="button button-light">Cancel</a>
    </div>
  </form>
</section>
