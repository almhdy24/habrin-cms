<?php if (!empty($flash)): ?>
  <?php foreach ($flash as $msg): ?>
    <div class="flash <?= $msg['type'] === 'error' ? 'flash-error' : 'flash-success' ?>">
      <?= htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
