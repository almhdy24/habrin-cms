<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $this->e($title ?? ($settings['site_title'] ?? 'Dark')) ?></title>
  <meta name="description" content="<?= $this->e($settings['site_description'] ?? '') ?>">
  <link rel="stylesheet" href="<?= $this->asset('themes/dark/assets/css/style.css') ?>">
  <?php $this->hook('head') ?>
</head>
<body>
  <?php $this->insert('partials/header') ?>
  <main class="container">
    <?php $this->insert('partials/flash', ['flash' => $flash ?? []]) ?>
    <?= $this->section('content') ?>
  </main>
  <?php $this->insert('partials/footer') ?>
  <?php $this->hook('body_end') ?>
</body>
</html>
