<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Admin', ENT_QUOTES, 'UTF-8') ?> - Habrin CMS</title>
  <meta name="theme-color" content="#0f172a">
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/admin.css') ?>">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
  <?php $this->hook('admin_head') ?>
</head>
<body>

<div class="admin-overlay" id="sidebarOverlay" aria-hidden="true"></div>
<div id="habrinToastContainer" class="toast-container" aria-live="polite"></div>

<div class="admin-wrapper">

  <aside class="admin-sidebar" id="adminSidebar" aria-label="Habrin CMS Admin Navigation">
    <div class="admin-sidebar-brand">
      <a href="/admin/dashboard" class="brand-link">
        <svg class="brand-mark" viewBox="0 0 40 40" width="32" height="32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <defs>
            <linearGradient id="habrinAdminGrad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#818cf8" />
              <stop offset="100%" stop-color="#4f46e5" />
            </linearGradient>
          </defs>
          <rect width="40" height="40" rx="10" fill="url(#habrinAdminGrad)" />
          <path d="M12 10V30M28 10V30M12 20H28" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
          <circle cx="20" cy="20" r="2.5" fill="#ffffff" />
        </svg>
        <div class="brand-info">
          <span class="brand-title">Habrin</span>
          <span class="brand-pill">CMS</span>
        </div>
      </a>
    </div>

    <nav class="admin-sidebar-nav">
      <?php
      $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
      $isActive = fn(string $route) => $currentPath === $route ? 'is-active' : '';
      $isSectionActive = fn(array $routes) => in_array($currentPath, $routes, true) ? 'is-active' : '';
      ?>

      <div class="nav-section-title">Content</div>

      <a href="/admin/dashboard" class="<?= $isActive('/admin/dashboard') ?>" <?= $currentPath === '/admin/dashboard' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="layout-dashboard"></i> <span>Dashboard</span>
      </a>

      <a href="/admin" class="<?= $isSectionActive(['/admin', '/admin/add']) ?>" <?= $currentPath === '/admin' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="file-text"></i> <span>Posts</span>
        <?php if (!empty($draftCount)): ?>
          <span class="badge badge-warning" title="<?= (int) $draftCount ?> drafts"><?= (int) $draftCount ?></span>
        <?php endif; ?>
      </a>

      <a href="/admin/pages" class="<?= $isActive('/admin/pages') ?>" <?= $currentPath === '/admin/pages' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="file"></i> <span>Pages</span>
      </a>

      <a href="/admin/media" class="<?= $isActive('/admin/media') ?>" <?= $currentPath === '/admin/media' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="image"></i> <span>Media Library</span>
      </a>

      <div class="nav-section-title">Structure</div>

      <a href="/admin/menu" class="<?= $isActive('/admin/menu') ?>" <?= $currentPath === '/admin/menu' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="menu"></i> <span>Navigation</span>
      </a>

      <a href="/admin/themes" class="<?= $isActive('/admin/themes') ?>" <?= $currentPath === '/admin/themes' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="palette"></i> <span>Themes</span>
      </a>

      <a href="/admin/plugins" class="<?= $isActive('/admin/plugins') ?>" <?= $currentPath === '/admin/plugins' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="puzzle"></i> <span>Plugins</span>
      </a>

      <div class="nav-section-title">Maintenance</div>

      <a href="/admin/backup" class="<?= $isActive('/admin/backup') ?>" <?= $currentPath === '/admin/backup' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="archive"></i> <span>Backups</span>
      </a>

      <a href="/admin/settings" class="<?= $isActive('/admin/settings') ?>" <?= $currentPath === '/admin/settings' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="settings"></i> <span>Settings</span>
      </a>

      <a href="/admin/system" class="<?= $isActive('/admin/system') ?>" <?= $currentPath === '/admin/system' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="cpu"></i> <span>System</span>
      </a>

      <a href="/admin/trash" class="<?= $isActive('/admin/trash') ?>" <?= $currentPath === '/admin/trash' ? 'aria-current="page"' : '' ?>>
        <i data-lucide="trash-2"></i> <span>Trash</span>
        <?php if (!empty($trashCount)): ?>
          <span class="badge badge-danger" aria-label="<?= (int) $trashCount ?> items in trash"><?= (int) $trashCount ?></span>
        <?php endif; ?>
      </a>
    </nav>

    <div class="admin-sidebar-footer">
      <a href="/" target="_blank" rel="noopener"><i data-lucide="external-link"></i> <span>Visit Site</span></a>
      <a href="/admin/logout" class="logout-link"><i data-lucide="log-out"></i> <span>Sign Out</span></a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-main-header">
      <div class="header-left">
        <button type="button" class="admin-sidebar-toggle" id="sidebarToggle" aria-label="Toggle navigation menu" aria-controls="adminSidebar" aria-expanded="false">
          <i data-lucide="menu"></i>
        </button>
        <div class="header-breadcrumb">
          <span class="text-muted">Habrin</span>
          <span class="text-muted">/</span>
          <h1><span><?= htmlspecialchars($title ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></span></h1>
        </div>
      </div>

      <div class="header-actions">
        <?php if ($currentPath !== '/admin/add'): ?>
          <a href="/admin/add" class="button button-sm button-primary">
            <i data-lucide="plus-circle"></i> <span class="hide-mobile">New Post</span>
          </a>
        <?php endif; ?>
        <a href="/" target="_blank" rel="noopener" class="button button-sm button-light" title="View live site">
          <i data-lucide="external-link"></i> <span class="hide-mobile">View Site</span>
        </a>
      </div>
    </header>

    <div class="admin-content-body">
      <?php $this->insert('partials/flash', ['flash' => $flash ?? []]) ?>
      <?= $this->section('content') ?>
    </div>
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
  sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
    if (window.innerWidth <= 768) closeSidebar();
  }));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
  window.addEventListener('resize', () => { if (window.innerWidth > 768) closeSidebar(); });

  // Global toast utility for Habrin CMS
  window.habrinToast = function (message, type = 'success') {
    const container = document.getElementById('habrinToastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.classList.add('show'); }, 10);
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 250);
    }, 2800);
  };
});
</script>

<?php $this->hook('admin_body_end') ?>
</body>
</html>
