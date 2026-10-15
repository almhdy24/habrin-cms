<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Installer', ENT_QUOTES, 'UTF-8') ?> - Habrin CMS</title>
  <meta name="theme-color" content="#0f172a">
  <link rel="stylesheet" href="<?= $this->asset('themes/default/assets/css/style.css') ?>">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
  <?php $this->hook('head') ?>

  <style>
    .installer-wrap {
      min-height: 100vh;
      min-height: 100dvh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1.25rem;
      background: radial-gradient(circle at top, rgba(99, 102, 241, 0.12), transparent 45%),
                  linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
    }

    .installer-box {
      background: #ffffff;
      width: 100%;
      max-width: 540px;
      padding: 2.25rem;
      border-radius: var(--radius-lg);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
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
      font-size: 0.85rem;
      font-weight: 600;
    }

    .installer-step.active {
      color: var(--primary);
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
            <defs>
              <linearGradient id="habrinInstGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#6366f1" />
                <stop offset="100%" stop-color="#4f46e5" />
              </linearGradient>
            </defs>
            <rect width="40" height="40" rx="10" fill="url(#habrinInstGrad)" />
            <path d="M12 10V30M28 10V30M12 20H28" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="20" cy="20" r="2.5" fill="#ffffff" />
          </svg>
        </div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Habrin CMS Setup</h1>
        <p class="text-muted" style="font-size: 0.9rem; margin: 0;">Guided initial configuration</p>
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