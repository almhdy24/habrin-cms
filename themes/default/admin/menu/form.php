<?php $this->layout('layouts/admin', ['title' => $item ? 'Edit Menu Item' : 'Add Menu Item']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <h2 class="mb-0"><?= $item ? 'Edit Menu Item' : 'Add Menu Item' ?></h2>
    <a href="/admin/menu" class="button button-light">
      <i data-lucide="arrow-left"></i> All Menu Items
    </a>
  </div>

  <form method="POST" class="card">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <div class="field">
      <label class="label" for="menuLabel"><i data-lucide="type"></i> Navigation Label</label>
      <input
        id="menuLabel"
        class="input"
        type="text"
        name="label"
        value="<?= htmlspecialchars((string) ($item['label'] ?? '')) ?>"
        placeholder="e.g. Documentation"
        required
      >
      <p class="field-hint"><i data-lucide="info"></i> The text displayed in the header navigation menu.</p>
    </div>

    <div class="field">
      <label class="label" for="menuUrl"><i data-lucide="link"></i> Destination URL</label>
      <input
        id="menuUrl"
        class="input"
        type="text"
        name="url"
        value="<?= htmlspecialchars((string) ($item['url'] ?? '')) ?>"
        placeholder="e.g. /about or https://example.com"
        required
      >
      <p class="field-hint"><i data-lucide="info"></i> Relative path (e.g. <code>/contact</code>) or absolute URL.</p>
    </div>

    <div class="field">
      <label class="label" for="menuOrder"><i data-lucide="list-ordered"></i> Display Order</label>
      <input
        id="menuOrder"
        class="input"
        type="number"
        name="order"
        value="<?= (int) ($item['order'] ?? 0) ?>"
        min="0"
      >
      <p class="field-hint"><i data-lucide="info"></i> Position integer; smaller values appear first in the menu bar.</p>
    </div>

    <div class="flex gap-1 mt-4">
      <button type="submit" class="button button-primary"><i data-lucide="save"></i> Save Menu Item</button>
      <a href="/admin/menu" class="button button-light">Cancel</a>
    </div>
  </form>
</section>
