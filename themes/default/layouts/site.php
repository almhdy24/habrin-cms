<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $this->e($title ?? ($settings['site_title'] ?? 'Habrin CMS')) ?></title>

  <!-- Meta tags for SEO -->
  <meta name="description" content="<?= $this->e($settings['site_description'] ?? '') ?>">
  <meta name="keywords" content="<?= $this->e($settings['site_keywords'] ?? '') ?>">

  <meta name="theme-color" content="#315C52">
  <link rel="icon" type="image/svg+xml" href="<?= $this->asset('themes/default/assets/img/favicon.svg') ?>">

  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/home.css') ?>">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

  <?php $this->hook('head') ?>
</head>
<body>

<?php $this->insert('partials/header') ?>

<main class="container main-content">
  <?php $this->insert('partials/flash', ['flash' => $flash ?? []]) ?>
  <?= $this->section('content') ?>
</main>

<?php $this->insert('partials/footer') ?>

<script>
  // Initialize Lucide icons
  lucide.createIcons();

  // Mobile menu toggle – pure JavaScript, no dependencies
  document.addEventListener('DOMContentLoaded', function() {
    const burger = document.querySelector('.navbar-burger');
    const nav = document.querySelector('.header-nav');

    if (burger && nav) {
      burger.addEventListener('click', function(e) {
        e.preventDefault();
        burger.classList.toggle('active');
        nav.classList.toggle('active');
      });
    }

    // Close menu when a link is clicked (optional)
    const navLinks = document.querySelectorAll('.header-nav .nav-link');
    navLinks.forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
          burger.classList.remove('active');
          nav.classList.remove('active');
        }
      });
    });
  });
</script>

<?php $this->hook('body_end') ?>

</body>
</html>
