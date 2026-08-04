<?php $this->layout('layouts/installer', [
  'title' => 'Install – System Check',
  'step' => 'system',
  'flash' => $flash ?? [],
]) ?>
<style>
  .system-check-list li {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
  }

  .system-check-list i {
  width: 18px;
  height: 18px;
  }
</style>
<h2 class="title">
  <i data-lucide="activity"></i>
  <span>System Check</span>
</h2>

<ul class="mb-4 system-check-list">
  <li>
    <i data-lucide="<?= $checks['php'] ? 'check-circle' : 'x-circle' ?>"
      class="<?= $checks['php'] ? 'has-text-success' : 'has-text-danger' ?>"></i>
    <span>PHP 8.1+</span>
  </li>

  <li>
    <i data-lucide="<?= $checks['json'] ? 'check-circle' : 'x-circle' ?>"
      class="<?= $checks['json'] ? 'has-text-success' : 'has-text-danger' ?>"></i>
    <span>JSON Support</span>
  </li>

  <li>
    <i data-lucide="<?= $checks['content'] ? 'check-circle' : 'x-circle' ?>"
      class="<?= $checks['content'] ? 'has-text-success' : 'has-text-danger' ?>"></i>
    <span>Writable <code>/content</code></span>
  </li>

  <li>
    <i data-lucide="<?= $checks['root'] ? 'check-circle' : 'x-circle' ?>"
      class="<?= $checks['root'] ? 'has-text-success' : 'has-text-danger' ?>"></i>
    <span>Writable Root Directory</span>
  </li>
</ul>

<form method="post">
  <button class="button is-fullwidth is-primary">
    <span>Continue</span>
    <i data-lucide="arrow-right"></i>
  </button>
</form>
