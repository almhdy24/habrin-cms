<header class="site-header">
  <div class="container">
    <div class="dateline-bar">
      <span><?= date('l, F j, Y') ?></span>
      <span class="dateline-center">Daily Independent Edition</span>
      <span>Published with Habrin</span>
    </div>

    <div class="masthead">
      <h1 class="site-title">
        <a href="/"><?= $this->e($settings['site_title'] ?? 'The Habrin Chronicle') ?></a>
      </h1>
      <?php if (!empty($settings['site_description'])): ?>
        <p class="site-tagline"><?= $this->e($settings['site_description']) ?></p>
      <?php endif; ?>
    </div>

    <div class="nav-bar-wrap">
      <button type="button" class="site-burger" aria-label="Toggle navigation menu" aria-expanded="false">
        <span class="burger-line"></span>
        <span class="burger-line"></span>
        <span class="burger-line"></span>
      </button>

      <nav class="main-nav">
        <a href="/"><?= __('nav.home') ?></a>
        <?php foreach ($menuItems ?? [] as $item): ?>
          <a href="<?= $this->e($item['url']) ?>"><?= $this->e($item['label']) ?></a>
        <?php endforeach; ?>
        <?php if ($this->is_admin()): ?>
          <a href="/admin/dashboard" class="admin-link"><i data-lucide="layout-dashboard"></i> <?= __('nav.dashboard') ?></a>
        <?php endif; ?>
        <span class="lang-selector" style="display: inline-flex; gap: 0.2rem; margin-left: 0.5rem;">
          <a href="/admin/lang/en" class="lang-pill <?= current_lang() === 'en' ? 'is-active' : '' ?>">EN</a>
          <a href="/admin/lang/ar" class="lang-pill <?= current_lang() === 'ar' ? 'is-active' : '' ?>">العربية</a>
          <a href="/admin/lang/fr" class="lang-pill <?= current_lang() === 'fr' ? 'is-active' : '' ?>">FR</a>
          <a href="/admin/lang/es" class="lang-pill <?= current_lang() === 'es' ? 'is-active' : '' ?>">ES</a>
        </span>
      </nav>
    </div>
  </div>
</header>
