<?php $this->layout('layouts/admin', ['title' => 'Edit Post']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="edit-3"></i> Edit Article</h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;">
        Article #<?= (int) $id ?> · Created <?= date('M j, Y', (int) $post['created_at']) ?>
        <?php if (!empty($post['updated_at']) && $post['updated_at'] !== $post['created_at']): ?>
          · Updated <?= date('M j, Y', (int) $post['updated_at']) ?>
        <?php endif; ?>
      </p>
    </div>
    <div class="flex gap-1">
      <a href="/admin" class="button button-light">
        <i data-lucide="arrow-left"></i> All Articles
      </a>
      <a href="/post/<?= htmlspecialchars($post['slug'] ?? '') ?>" class="button button-light" target="_blank" rel="noopener">
        <i data-lucide="eye"></i> Preview
      </a>
    </div>
  </div>

  <form method="POST" action="/admin/edit/<?= (int) $id ?>" id="postForm">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-start;">

      <!-- Main Editor Column -->
      <div style="flex: 1; min-width: min(100%, 540px);">
        <div class="card mb-4" style="padding: 1.75rem;">
          <div class="field">
            <label class="label" for="postTitle" style="font-size: 1.1rem; font-weight: 700;">
              Article Title <span class="text-muted" style="font-size: 0.85rem; font-weight: 400;">(required)</span>
            </label>
            <input
              id="postTitle"
              class="input"
              type="text"
              name="title"
              value="<?= htmlspecialchars((string) ($old['title'] ?? $post['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
              required
              autofocus
              style="font-size: 1.25rem; font-weight: 700; padding: 0.85rem 1rem;"
            >
          </div>

          <div class="field mb-0">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
              <label class="label mb-0" for="content">Content</label>
              <div class="text-muted" style="font-size: 0.82rem; display: flex; gap: 0.85rem;">
                <span><i data-lucide="file-text" style="width:14px;height:14px;display:inline;"></i> <strong id="wordCount">0</strong> words</span>
                <span>Markdown Supported</span>
              </div>
            </div>
            <textarea
              id="content"
              class="textarea simple-editor"
              name="content"
              rows="16"
              required
            ><?= htmlspecialchars((string) ($old['content'] ?? $post['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Right Publishing Sidebar -->
      <div style="width: 320px; max-width: 100%;">
        <div class="card mb-3" style="padding: 1.5rem;">
          <h3 class="mb-3" style="font-size: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="send" style="color: var(--primary);"></i>
            <span>Publishing Settings</span>
          </h3>

          <div class="field">
            <label class="label" for="postStatus">Status</label>
            <?php $currentStatus = $post['status'] ?? 'published'; ?>
            <select id="postStatus" class="input" name="status">
              <option value="published" <?= $currentStatus === 'published' ? 'selected' : '' ?>>
                🟢 Published (Public)
              </option>
              <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>
                🟡 Draft (Private)
              </option>
            </select>
            <div class="help">Drafts can be previewed by logged-in administrators.</div>
          </div>

          <div class="field">
            <label class="label" for="postSlug">URL Slug</label>
            <input id="postSlug" class="input" type="text" name="slug" value="<?= htmlspecialchars((string) ($post['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" readonly style="background: var(--bg-soft); color: var(--text-light); font-family: var(--font-mono); font-size: 0.85rem;">
            <div class="help">Generated automatically from the article title.</div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1.5rem;">
            <button type="submit" class="button button-primary button-block button-lg">
              <i data-lucide="save"></i> Update Article
            </button>
            <a href="/admin/media" target="_blank" rel="noopener" class="button button-light button-block">
              <i data-lucide="image"></i> Media Library
            </a>
          </div>
        </div>

        <!-- Move to Trash Card -->
        <div class="card" style="padding: 1.25rem; border-color: rgba(239, 68, 68, 0.25);">
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
            <span class="text-muted" style="font-size: 0.85rem;">Trash this article:</span>
            <button type="submit" form="deletePostForm" class="button button-sm button-danger" onclick="return confirm('Move this post to trash?')">
              <i data-lucide="trash-2"></i> Move to Trash
            </button>
          </div>
        </div>
      </div>

    </div>
  </form>

  <form id="deletePostForm" method="POST" action="/admin/delete/<?= (int) $id ?>" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
  </form>
</section>

<!-- EasyMDE Editor Integration -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const textarea = document.getElementById('content');
  const counter = document.getElementById('wordCount');
  if (!textarea || typeof EasyMDE === 'undefined') return;

  const editor = new EasyMDE({
    element: textarea,
    spellChecker: false,
    forceSync: true,
    status: false,
    toolbar: [
      "bold", "italic", "heading", "|",
      "quote", "unordered-list", "ordered-list", "|",
      "link", "image", "code", "table", "|",
      "preview", "fullscreen"
    ]
  });

  const updateCount = () => {
    const text = editor.value().trim();
    const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
    if (counter) counter.textContent = words;
  };

  editor.codemirror.on('change', updateCount);
  updateCount();
});
</script>
