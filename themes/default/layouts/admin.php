<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Admin', ENT_QUOTES, 'UTF-8') ?> - Admin Panel</title>
  <meta name="theme-color" content="#1e293b">
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/admin.css') ?>">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
  <?php $this->hook('admin_head') ?>
</head>
<body>

<div class="admin-overlay" id="sidebarOverlay" aria-hidden="true"></div>

<div class="admin-wrapper">

  <aside class="admin-sidebar" id="adminSidebar" aria-label="Admin navigation">
    <div class="admin-sidebar-brand">
      <i data-lucide="layout-dashboard"></i>
      <span>Admin Panel</span>
    </div>

    <nav class="admin-sidebar-nav">
      <?php
      $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
      $isActive = fn($route) => $currentPath === $route ? 'is-active' : '';
      ?>
      <a href="/admin/dashboard" class="<?= $isActive('/admin/dashboard') ?>" <?= $currentPath === '/admin/dashboard' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="layout-dashboard"></i> <span>Dashboard</span>
      </a>
      <a href="/admin" class="<?= $isActive('/admin') ?>" <?= $currentPath === '/admin' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="file-text"></i> <span>Posts</span>
      </a>
      <a href="/admin/pages" class="<?= $isActive('/admin/pages') ?>" <?= $currentPath === '/admin/pages' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="file"></i> <span>Pages</span>
      </a>
      <a href="/admin/menu" class="<?= $isActive('/admin/menu') ?>" <?= $currentPath === '/admin/menu' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="menu"></i> <span>Menu</span>
      </a>
      <a href="/admin/add" class="<?= $isActive('/admin/add') ?>" <?= $currentPath === '/admin/add' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="plus-circle"></i> <span>Add Post</span>
      </a>
      <a href="/admin/trash" class="<?= $isActive('/admin/trash') ?>" <?= $currentPath === '/admin/trash' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="trash-2"></i> <span>Trash</span>
        <?php if (!empty($trashCount)): ?>
          <span class="badge" aria-label="<?= (int) $trashCount ?> items in trash"><?= (int) $trashCount ?></span>
        <?php endif; ?>
      </a>
      <a href="/admin/plugins" class="<?= $isActive('/admin/plugins') ?>" <?= $currentPath === '/admin/plugins' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="puzzle"></i> <span>Plugins</span>
      </a>
      <a href="/admin/themes" class="<?= $isActive('/admin/themes') ?>" <?= $currentPath === '/admin/themes' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="palette"></i> <span>Themes</span>
      </a>
      <a href="/admin/settings" class="<?= $isActive('/admin/settings') ?>" <?= $currentPath === '/admin/settings' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="settings"></i> <span>Settings</span>
      </a>
      <a href="/admin/system" class="<?= $isActive('/admin/system') ?>" <?= $currentPath === '/admin/system' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="info"></i> <span>System</span>
      </a>
    </nav>

    <div class="admin-sidebar-footer">
      <a href="/" target="_self"><i data-lucide="external-link"></i> <span>View Site</span></a>
      <a href="/admin/logout"><i data-lucide="log-out"></i> <span>Logout</span></a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-main-header">
      <div class="flex items-center gap-1">
        <button type="button" class="admin-sidebar-toggle" id="sidebarToggle" aria-label="Open navigation menu" aria-controls="adminSidebar" aria-expanded="false">
          <i data-lucide="menu"></i>
        </button>
        <h1><i data-lucide="file-text"></i> <span><?= htmlspecialchars($title ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></span></h1>
      </div>
    </header>

    <?php $this->insert('partials/flash', ['flash' => $flash ?? []]) ?>
    <?= $this->section('content') ?>
  </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.lucide) lucide.createIcons();

  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const toggle = document.getElementById('sidebarToggle');
  if (!sidebar || !overlay || !toggle) return;

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('active');
    toggle.setAttribute('aria-expanded', 'true');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    toggle.setAttribute('aria-expanded', 'false');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }
  function toggleSidebar() { sidebar.classList.contains('open') ? closeSidebar() : openSidebar(); }

  toggle.addEventListener('click', toggleSidebar);
  overlay.addEventListener('click', closeSidebar);
  sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', closeSidebar));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
  window.addEventListener('resize', () => { if (window.innerWidth > 768) closeSidebar(); });
});
</script>

<?php $this->hook('admin_body_end') ?>
</body>
</html>
