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
        <thead>
          <tr>
            <th style="width: 80px;">Order</th>
            <th>Label</th>
            <th>URL</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $total = count($items); ?>
          <?php foreach ($items as $idx => $item): ?>
            <tr>
              <td>
                <div class="flex items-center gap-1">
                  <span class="badge" style="background: var(--bg-soft); color: var(--text-muted); font-size: 0.8rem; font-family: var(--font-mono); padding: 0.2rem 0.45rem;">
                    <?= $idx + 1 ?>
                  </span>
                  <div class="flex flex-col" style="gap: 2px;">
                    <?php if ($idx > 0): ?>
                      <form method="POST" action="/admin/menu/moveUp/<?= $idx ?>" style="margin: 0; display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="button button-sm button-light" style="padding: 1px 4px; line-height: 1; height: 18px;" title="Move Up">
                          <i data-lucide="chevron-up" style="width: 12px; height: 12px;"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <span style="height: 18px; width: 22px; display: inline-block;"></span>
                    <?php endif; ?>

                    <?php if ($idx < $total - 1): ?>
                      <form method="POST" action="/admin/menu/moveDown/<?= $idx ?>" style="margin: 0; display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="button button-sm button-light" style="padding: 1px 4px; line-height: 1; height: 18px;" title="Move Down">
                          <i data-lucide="chevron-down" style="width: 12px; height: 12px;"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <span style="height: 18px; width: 22px; display: inline-block;"></span>
                    <?php endif; ?>
                  </div>
                </div>
              </td>
              <td><strong><?= htmlspecialchars($item['label']) ?></strong></td>
              <td><code style="font-size: 0.85rem; color: var(--primary);"><?= htmlspecialchars($item['url']) ?></code></td>
              <td style="text-align: right;">
                <div class="flex justify-end gap-1">
                  <a href="/admin/menu/edit/<?= $idx ?>" class="button button-sm button-light" title="Edit Item">
                    <i data-lucide="edit"></i> Edit
                  </a>
                  <a href="/admin/menu/delete/<?= $idx ?>" class="button button-sm button-danger" onclick="return confirm('Delete this menu item?')" title="Delete Item">
                    <i data-lucide="trash-2"></i> Delete
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
