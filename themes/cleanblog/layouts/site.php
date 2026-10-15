<!DOCTYPE html>
<html lang="<?= current_lang() ?>" dir="<?= current_dir() ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $this->e($title ?? ($settings['site_title'] ?? 'CleanBlog')) ?></title>
  <meta name="description" content="<?= $this->e($settings['site_description'] ?? '') ?>">
  <link rel="icon" type="image/svg+xml" href="<?= $this->asset('themes/default/assets/img/favicon.svg') ?>">
  <link rel="stylesheet" href="<?= $this->asset('themes/cleanblog/assets/css/style.css') ?>">
  <script src="<?= $this->asset('assets/lucide.min.js') ?>"></script>
  <?php $this->hook('head') ?>
</head>
<body>
  <?php $this->insert('partials/header') ?>

  <main class="container">
    <?php $this->insert('partials/flash', ['flash' => $flash ?? []]) ?>
    <?= $this->section('content') ?>
  </main>

  <?php $this->insert('partials/footer') ?>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.lucide) {
        lucide.createIcons();
      }
      const burger = document.querySelector('.site-burger');
      const nav = document.querySelector('.site-nav');
      if (burger && nav) {
        burger.addEventListener('click', function () {
          const active = nav.classList.toggle('active');
          burger.setAttribute('aria-expanded', active ? 'true' : 'false');
        });
      }
    });
  </script>
  <?php $this->hook('body_end') ?>
</body>
</html>
