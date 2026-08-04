<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Login') ?></title>
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/style.css') ?>">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body style="background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">

  <div style="width: 100%; max-width: 420px;">
    <?= $this->section('content') ?>
  </div>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
