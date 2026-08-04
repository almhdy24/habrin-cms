<header class="site-header">
  <div class="container">
    <a href="/" class="site-title"><?= $this->e($settings['site_title'] ?? 'Dark') ?></a>
    <nav class="site-nav">
      <a href="/">Home</a>
      <?php foreach ($menuItems ?? [] as $item): ?>
        <a href="<?= $this->e($item['url']) ?>"><?= $this->e($item['label']) ?></a>
      <?php endforeach; ?>
      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
        <a href="/admin/dashboard" class="admin-link">Admin</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
