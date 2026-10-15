<?php $this->layout('layouts/admin', ['title' => 'Settings']) ?>

<section>

  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <h2 class="mb-0"><i data-lucide="settings"></i> Settings</h2>
    <a href="/admin/dashboard" class="button button-light">
      <i data-lucide="arrow-left"></i> Back
    </a>
  </div>

  <form method="POST" action="/admin/settings" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

    <h3>General</h3>
    <div class="field">
      <label class="label"><i data-lucide="type"></i> Site Title</label>
      <input class="input" type="text" name="site_title" value="<?= htmlspecialchars($old['site_title'] ?? $settings['site_title'] ?? '') ?>" placeholder="Habrin CMS">
    </div>

    <div class="field">
      <label class="label"><i data-lucide="align-left"></i> Site Description</label>
      <textarea class="textarea" name="site_description" rows="2" placeholder="Brief description"><?= htmlspecialchars($old['site_description'] ?? $settings['site_description'] ?? '') ?></textarea>
    </div>

    <div class="field">
      <label class="label"><i data-lucide="search"></i> SEO Keywords</label>
      <input class="input" type="text" name="site_keywords" value="<?= htmlspecialchars($old['site_keywords'] ?? $settings['site_keywords'] ?? '') ?>" placeholder="habrin, blog, cms, php">
    </div>

    <hr>

    <h3>Hero Section</h3>
    <div class="field">
      <label class="label"><i data-lucide="heading"></i> Hero Heading</label>
      <input class="input" type="text" name="hero_heading" value="<?= htmlspecialchars($old['hero_heading'] ?? $settings['hero_heading'] ?? '') ?>" placeholder="Welcome to Habrin CMS">
    </div>

    <div class="field">
      <label class="label"><i data-lucide="align-left"></i> Hero Description</label>
      <textarea class="textarea" name="hero_description" rows="2" placeholder="A simple, fast, and secure flat-file CMS."><?= htmlspecialchars($old['hero_description'] ?? $settings['hero_description'] ?? '') ?></textarea>
    </div>

    <div class="field">
      <label class="label"><i data-lucide="button"></i> Hero Button Text</label>
      <input class="input" type="text" name="hero_button_text" value="<?= htmlspecialchars($old['hero_button_text'] ?? $settings['hero_button_text'] ?? '') ?>" placeholder="Get Started">
    </div>

    <div class="field">
      <label class="label"><i data-lucide="link"></i> Hero Button Link</label>
      <input class="input" type="text" name="hero_button_link" value="<?= htmlspecialchars($old['hero_button_link'] ?? $settings['hero_button_link'] ?? '') ?>" placeholder="/about">
    </div>

    <div class="flex flex-wrap gap-1 mt-4">
      <button type="submit" class="button button-primary"><i data-lucide="save"></i> Save Settings</button>
      <a href="/admin/dashboard" class="button button-light"><i data-lucide="x"></i> Cancel</a>
    </div>
  </form>

  <?php $this->hook('admin_settings_after') ?>

</section>
