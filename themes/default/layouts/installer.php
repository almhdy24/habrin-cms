<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Installer', ENT_QUOTES, 'UTF-8') ?> - Habrin CMS</title>
  <meta name="theme-color" content="#315C52">
  <link rel="icon" type="image/svg+xml" href="<?= $this->asset('themes/default/assets/img/favicon.svg') ?>">
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/style.css') ?>">
  <script src="<?= $this->asset('assets/lucide.min.js') ?>"></script>
  <?php $this->hook('head') ?>

  <style>
    .installer-wrap {
      min-height: 100vh;
      min-height: 100dvh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2.5rem 1.25rem;
      background: var(--paper);
    }

    .installer-box {
      background: var(--white);
      width: 100%;
      max-width: 540px;
      padding: 2.5rem;
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-md);
    }

    .installer-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .installer-header .logo-wrap {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 0.85rem;
    }

    .installer-steps {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
      padding-bottom: 1.25rem;
      border-bottom: 1px solid var(--border);
      position: relative;
    }

    .installer-step {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--text-muted);
      font-size: 0.88rem;
      font-weight: 600;
    }

    .installer-step.active {
      color: var(--primary);
      font-weight: 700;
    }

    .installer-step.done {
      color: var(--success);
    }

    .installer-step i, .installer-step svg {
      width: 18px;
      height: 18px;
    }

    @media (max-width: 480px) {
      .installer-box { padding: 1.5rem; }
      .installer-steps { font-size: 0.78rem; gap: 0.25rem; }
    }
  </style>
</head>
<body>

  <div class="installer-wrap">
    <div class="installer-box">

      <div class="installer-header">
        <div class="logo-wrap">
          <svg viewBox="0 0 40 40" width="48" height="48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="40" height="40" rx="8" fill="#315C52" />
            <rect x="0.5" y="0.5" width="39" height="39" rx="7.5" stroke="#274A42" stroke-width="1" />
            <path d="M12 11V29" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M28 11V29" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M12 20H28" stroke="#F7F6F2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <rect x="18.5" y="18.5" width="3" height="3" fill="#FFFFFF" rx="0.5" />
          </svg>
        </div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem; color: var(--ink);">Habrin CMS Setup</h1>
        <p class="text-muted" style="font-size: 0.9rem; margin: 0;">Publish without the clutter.</p>
      </div>

      <!-- Stepper -->
      <div class="installer-steps">
        <div class="installer-step <?= $step === 'system' ? 'active' : ($step !== 'system' ? 'done' : '') ?>">
          <i data-lucide="<?= $step !== 'system' ? 'check-circle' : 'cpu' ?>"></i>
          <span>1. System</span>
        </div>
        <div class="installer-step <?= $step === 'admin' ? 'active' : ($step === 'finish' ? 'done' : '') ?>">
          <i data-lucide="<?= $step === 'finish' ? 'check-circle' : 'user-cog' ?>"></i>
          <span>2. Admin</span>
        </div>
        <div class="installer-step <?= $step === 'finish' ? 'active' : '' ?>">
          <i data-lucide="shield-check"></i>
          <span>3. Complete</span>
        </div>
      </div>

      <?php $this->insert('partials/flash', ['flash' => $flash ?? []]) ?>
      <?= $this->section('content') ?>

    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
    });
  </script>

  <?php $this->hook('body_end') ?>
</body>
</html>