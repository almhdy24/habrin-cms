<?php $this->layout('layouts/admin', ['title' => 'Pages']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <h2 class="mb-0"><i data-lucide="file-text"></i> Pages</h2>
    <a href="/admin/pages/create" class="button button-primary">
      <i data-lucide="plus-circle"></i> New Page
    </a>
  </div>

  <?php if (empty($pages)): ?>
    <div class="notification notification-success">No pages yet. <a href="/admin/pages/create">Create one</a></div>
  <?php else: ?>
    <div class="table-container">
      <table class="table">
        <thead><tr><th>Title</th><th>Slug</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($pages as $slug => $page): ?>
            <tr>
              <td><?= htmlspecialchars($page['title']) ?></td>
              <td><code><?= htmlspecialchars($slug) ?></code></td>
              <td>
                <div class="flex gap-1">
                  <a href="/admin/pages/edit/<?= htmlspecialchars($slug) ?>" class="button button-sm button-light"><i data-lucide="edit"></i></a>
                  <a href="/admin/pages/delete/<?= htmlspecialchars($slug) ?>" class="button button-sm button-danger" onclick="return confirm('Delete this page?')"><i data-lucide="trash-2"></i></a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
