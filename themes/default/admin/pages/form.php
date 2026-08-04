<?php $this->layout('layouts/admin', ['title' => $page ? 'Edit Page' : 'Create Page']) ?>

<section>
  <h2 class="mb-4"><?= $page ? 'Edit Page' : 'Create Page' ?></h2>

  <form method="POST" class="card">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

    <div class="field">
      <label class="label"><i data-lucide="type"></i> Title</label>
      <input class="input" type="text" name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required>
    </div>

    <?php if (!$page): ?>
      <div class="field">
        <label class="label"><i data-lucide="link"></i> Slug (URL)</label>
        <input class="input" type="text" name="slug" placeholder="about" required>
        <div class="help">Will appear as /slug</div>
      </div>
    <?php endif; ?>

    <div class="field">
      <label class="label"><i data-lucide="align-left"></i> Content (Markdown)</label>
      <textarea class="textarea" name="content" rows="12" required><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
    </div>

    <div class="flex gap-1">
      <button type="submit" class="button button-primary"><i data-lucide="save"></i> Save</button>
      <a href="/admin/pages" class="button button-light">Cancel</a>
    </div>
  </form>
</section>
