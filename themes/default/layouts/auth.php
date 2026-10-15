<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Sign In', ENT_QUOTES, 'UTF-8') ?> - Habrin CMS</title>
  <meta name="theme-color" content="#0f172a">
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/style.css') ?>">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
</head>
<body class="auth-page">

  <div class="auth-container">
    <?= $this->section('content') ?>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
    });
  </script>
</body>
</html>
