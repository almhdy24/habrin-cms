<?php $this->layout('layouts/admin', ['title' => $page ? 'Edit Page' : 'Create Page']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <h2 class="mb-0"><?= $page ? 'Edit Page' : 'Create Page' ?></h2>
    <a href="/admin/pages" class="button button-light">
      <i data-lucide="arrow-left"></i> All Pages
    </a>
  </div>

  <form method="POST" class="card">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <div class="field">
      <label class="label" for="pageTitle"><i data-lucide="type"></i> Page Title</label>
      <input
        id="pageTitle"
        class="input"
        type="text"
        name="title"
        placeholder="e.g. About Our Publication"
        value="<?= htmlspecialchars((string) ($page['title'] ?? '')) ?>"
        required
      >
      <p class="field-hint"><i data-lucide="info"></i> The public title displayed at the top of the standalone page.</p>
    </div>

    <?php if (!$page): ?>
      <div class="field">
        <label class="label" for="pageSlug"><i data-lucide="link"></i> Page Slug (URL)</label>
        <input
          id="pageSlug"
          class="input"
          type="text"
          name="slug"
          placeholder="e.g. about"
          required
        >
        <p class="field-hint"><i data-lucide="info"></i> Unique path where this page will be served (e.g. <code>/about</code>).</p>
      </div>
    <?php endif; ?>

    <div class="field">
      <label class="label" for="pageContent"><i data-lucide="align-left"></i> Content (Markdown)</label>
      <textarea
        id="pageContent"
        class="textarea"
        name="content"
        rows="12"
        placeholder="Write the page body in Markdown..."
        required
      ><?= htmlspecialchars((string) ($page['content'] ?? '')) ?></textarea>
      <p class="field-hint"><i data-lucide="info"></i> Markdown syntax and formatting are supported.</p>
    </div>

    <div class="flex gap-1 mt-4">
      <button type="submit" class="button button-primary"><i data-lucide="save"></i> Save Page</button>
      <a href="/admin/pages" class="button button-light">Cancel</a>
    </div>
  </form>
</section>
