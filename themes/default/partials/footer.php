<footer class="site-footer">
  <div class="container">
    <div class="footer-content">
      <p>
        &copy; <?= date('Y') ?>
        <a href="/"><?= $this->e($settings['site_title'] ?? 'Habrin CMS') ?></a>
        &mdash; Built with <i data-lucide="heart" style="width:16px;height:16px;display:inline;color:var(--danger);"></i>
        and <a href="https://github.com/almhdy24/pico-json-cms" target="_blank" rel="noopener">Habrin CMS</a>
      </p>
      <?php $this->hook('footer') ?>
    </div>
  </div>
</footer>
