<?php $this->layout('layouts/admin', ['title' => 'Menu']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <h2 class="mb-0"><i data-lucide="menu"></i> Menu Items</h2>
    <a href="/admin/menu/create" class="button button-primary">
      <i data-lucide="plus-circle"></i> Add Item
    </a>
  </div>

  <?php if (empty($items)): ?>
    <div class="notification notification-success">No menu items. <a href="/admin/menu/create">Add one</a></div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead><tr><th>Label</th><th>URL</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($items as $idx => $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['label']) ?></td>
              <td><?= htmlspecialchars($item['url']) ?></td>
              <td><?= (int) ($item['order'] ?? 0) ?></td>
              <td>
                <div class="flex gap-1">
                  <a href="/admin/menu/edit/<?= $idx ?>" class="button button-sm button-light"><i data-lucide="edit"></i></a>
                  <a href="/admin/menu/delete/<?= $idx ?>" class="button button-sm button-danger" onclick="return confirm('Delete this item?')"><i data-lucide="trash-2"></i></a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
