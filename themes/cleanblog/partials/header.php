<header class="site-header">
  <div class="container header-container">
    <div class="header-brand">
      <a href="/" class="site-title"><?= $this->e($settings['site_title'] ?? 'CleanBlog') ?></a>
      <?php if (!empty($settings['site_description'])): ?>
        <span class="site-tagline"><?= $this->e($settings['site_description']) ?></span>
      <?php endif; ?>
    </div>

    <button type="button" class="site-burger" aria-label="Toggle navigation menu" aria-expanded="false">
      <span class="burger-line"></span>
      <span class="burger-line"></span>
      <span class="burger-line"></span>
    </button>

    <nav class="site-nav">
      <a href="/">Home</a>
      <?php foreach ($menuItems ?? [] as $item): ?>
        <a href="<?= $this->e($item['url']) ?>"><?= $this->e($item['label']) ?></a>
      <?php endforeach; ?>
      <?php if ($this->is_admin()): ?>
        <a href="/admin/dashboard" class="admin-link"><i data-lucide="layout-dashboard"></i> Admin</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
