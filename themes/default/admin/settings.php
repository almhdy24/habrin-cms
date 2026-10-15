<?php $this->layout('layouts/admin', ['title' => 'Settings']) ?>

<section>

  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <h2 class="mb-0"><i data-lucide="settings"></i> System Settings</h2>
    <a href="/admin/dashboard" class="button button-light">
      <i data-lucide="arrow-left"></i> Dashboard
    </a>
  </div>

  <form method="POST" action="/admin/settings" class="card">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <h3 style="font-size: 1.15rem; color: var(--ink); margin-bottom: 1.25rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
      <i data-lucide="sliders" style="color: var(--primary);"></i> General Configuration
    </h3>

    <div class="field">
      <label class="label" for="settingSiteTitle"><i data-lucide="type"></i> Site Title</label>
      <input
        id="settingSiteTitle"
        class="input"
        type="text"
        name="site_title"
        value="<?= htmlspecialchars((string) ($old['site_title'] ?? $settings['site_title'] ?? '')) ?>"
        placeholder="e.g. Habrin CMS"
        required
      >
      <p class="field-hint"><i data-lucide="info"></i> Displayed in your browser tab and header brand mark.</p>
    </div>

    <div class="field">
      <label class="label" for="settingSiteDesc"><i data-lucide="align-left"></i> Site Description</label>
      <textarea
        id="settingSiteDesc"
        class="textarea"
        name="site_description"
        rows="2"
        placeholder="Publish without the clutter."
      ><?= htmlspecialchars((string) ($old['site_description'] ?? $settings['site_description'] ?? '')) ?></textarea>
      <p class="field-hint"><i data-lucide="info"></i> Default description used for search engine indexing and social meta.</p>
    </div>

    <div class="field">
      <label class="label" for="settingSiteKeywords"><i data-lucide="search"></i> SEO Keywords</label>
      <input
        id="settingSiteKeywords"
        class="input"
        type="text"
        name="site_keywords"
        value="<?= htmlspecialchars((string) ($old['site_keywords'] ?? $settings['site_keywords'] ?? '')) ?>"
        placeholder="habrin, blog, cms, php, markdown"
      >
      <p class="field-hint"><i data-lucide="info"></i> Comma-separated topics or tags characterizing your publication.</p>
    </div>

    <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border);">

    <h3 style="font-size: 1.15rem; color: var(--ink); margin-bottom: 1.25rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
      <i data-lucide="layout" style="color: var(--primary);"></i> Homepage Hero Section
    </h3>

    <div class="field">
      <label class="label" for="settingHeroHeading"><i data-lucide="heading"></i> Hero Heading</label>
      <input
        id="settingHeroHeading"
        class="input"
        type="text"
        name="hero_heading"
        value="<?= htmlspecialchars((string) ($old['hero_heading'] ?? $settings['hero_heading'] ?? '')) ?>"
        placeholder="Welcome to Habrin CMS"
      >
      <p class="field-hint"><i data-lucide="info"></i> Main greeting or headline displayed on the homepage banner.</p>
    </div>

    <div class="field">
      <label class="label" for="settingHeroDesc"><i data-lucide="align-left"></i> Hero Description</label>
      <textarea
        id="settingHeroDesc"
        class="textarea"
        name="hero_description"
        rows="2"
        placeholder="Publish without the clutter. A modern, fast, and secure flat-file CMS."
      ><?= htmlspecialchars((string) ($old['hero_description'] ?? $settings['hero_description'] ?? '')) ?></textarea>
      <p class="field-hint"><i data-lucide="info"></i> Supporting explanation beneath your homepage headline.</p>
    </div>

    <div class="field">
      <label class="label" for="settingHeroBtnText"><i data-lucide="square"></i> Hero Button Text</label>
      <input
        id="settingHeroBtnText"
        class="input"
        type="text"
        name="hero_button_text"
        value="<?= htmlspecialchars((string) ($old['hero_button_text'] ?? $settings['hero_button_text'] ?? '')) ?>"
        placeholder="Get Started"
      >
      <p class="field-hint"><i data-lucide="info"></i> Call-to-action button label. Leave blank to omit.</p>
    </div>

    <div class="field">
      <label class="label" for="settingHeroBtnLink"><i data-lucide="link"></i> Hero Button Link</label>
      <input
        id="settingHeroBtnLink"
        class="input"
        type="text"
        name="hero_button_link"
        value="<?= htmlspecialchars((string) ($old['hero_button_link'] ?? $settings['hero_button_link'] ?? '')) ?>"
        placeholder="/about"
      >
      <p class="field-hint"><i data-lucide="info"></i> Destination URL when visitors click your call-to-action button.</p>
    </div>

    <div class="flex flex-wrap gap-1 mt-4">
      <button type="submit" class="button button-primary">
        <i data-lucide="save"></i> Save Configuration
      </button>
      <a href="/admin/dashboard" class="button button-light">
        <i data-lucide="x"></i> Cancel
      </a>
    </div>
  </form>

  <?php $this->hook('admin_settings_after') ?>

</section>
