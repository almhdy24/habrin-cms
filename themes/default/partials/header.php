<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <div class="header-brand">
        <a href="/" class="brand-link">
          <svg viewBox="0 0 40 40" width="32" height="32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect width="40" height="40" rx="8" fill="#315C52" />
            <rect x="0.5" y="0.5" width="39" height="39" rx="7.5" stroke="#274A42" stroke-width="1" />
            <path d="M12 11V29" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M28 11V29" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M12 20H28" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <rect x="18.5" y="18.5" width="3" height="3" fill="#FFFFFF" rx="0.5" />
          </svg>
          <div>
            <span class="brand-name"><?= $this->e($settings['site_title'] ?? 'Habrin CMS') ?></span>
            <span class="brand-tagline-sub">Publish without the clutter</span>
          </div>
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
        <a href="/" class="nav-link <?= $isActive('/') ?>"><i data-lucide="home"></i> <?= __('nav.home') ?></a>
        <?php foreach ($menuItems ?? [] as $item): ?>
          <a href="<?= $this->e($item['url']) ?>" class="nav-link <?= $isActive($item['url']) ?>">
            <?= $this->e($item['label']) ?>
          </a>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
          <a href="/admin/dashboard" class="nav-link admin-link"><i data-lucide="layout-dashboard"></i> <?= __('nav.dashboard') ?></a>
        <?php endif; ?>

        <div class="lang-selector" style="margin-left: 0.5rem; display: inline-flex;">
          <a href="/admin/lang/en" class="lang-pill <?= current_lang() === 'en' ? 'is-active' : '' ?>">EN</a>
          <a href="/admin/lang/ar" class="lang-pill <?= current_lang() === 'ar' ? 'is-active' : '' ?>">العربية</a>
          <a href="/admin/lang/fr" class="lang-pill <?= current_lang() === 'fr' ? 'is-active' : '' ?>">FR</a>
          <a href="/admin/lang/es" class="lang-pill <?= current_lang() === 'es' ? 'is-active' : '' ?>">ES</a>
        </div>
      </nav>
    </div>
  </div>
</header>
