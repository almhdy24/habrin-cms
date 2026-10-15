<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <div class="header-brand">
        <a href="/" class="brand-link">
          <svg viewBox="0 0 40 40" width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <defs>
              <linearGradient id="habrinNavGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#6366f1" />
                <stop offset="100%" stop-color="#4f46e5" />
              </linearGradient>
            </defs>
            <rect width="40" height="40" rx="9" fill="url(#habrinNavGrad)" />
            <path d="M12 10V30M28 10V30M12 20H28" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="20" cy="20" r="2.5" fill="#ffffff" />
          </svg>
          <span class="brand-name"><?= $this->e($settings['site_title'] ?? 'Habrin CMS') ?></span>
        </a>
      </div>

      <button class="navbar-burger" aria-label="Toggle navigation menu" aria-expanded="false" type="button">
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
