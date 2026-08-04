<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <div class="header-brand">
        <a href="/" class="brand-link">
          <i data-lucide="pen-tool" class="brand-icon"></i>
          <span class="brand-name"><?= $this->e($settings['site_title'] ?? 'Pico CMS') ?></span>
        </a>
      </div>

      <button class="navbar-burger" aria-label="Toggle menu" type="button">
        <span></span><span></span><span></span>
      </button>

      <nav class="header-nav">
        <?php
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $isActive = fn($url) => $currentPath === $url ? 'active' : '';
        ?>
        <a href="/" class="nav-link <?= $isActive('/') ?>"><i data-lucide="home"></i> Home</a>
        <?php foreach ($menuItems ?? [] as $item): ?>
          <a href="<?= $this->e($item['url']) ?>" class="nav-link <?= $isActive($item['url']) ?>">
            <?= $this->e($item['label']) ?>
          </a>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
          <a href="/admin/dashboard" class="nav-link admin-link"><i data-lucide="layout-dashboard"></i> Admin</a>
        <?php endif; ?>
      </nav>
    </div>
  </div>
</header>
