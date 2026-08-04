<?php if (!empty($flash)): ?>
  <?php foreach ($flash as $msg): ?>
    <div class="notification notification-<?= $msg['type'] === 'error' ? 'danger' : 'success' ?>">
      <i data-lucide="<?= $msg['type'] === 'error' ? 'alert-circle' : 'check-circle' ?>"></i>
      <span><?= htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
