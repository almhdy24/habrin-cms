<?php $this->layout('layouts/admin', ['title' => 'New Post']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="file-plus"></i> Write New Article</h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;">Author content using Markdown or HTML syntax.</p>
    </div>
    <a href="/admin" class="button button-light">
      <i data-lucide="arrow-left"></i> All Articles
    </a>
  </div>

  <form method="POST" action="/admin/add" id="postForm">
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
              placeholder="e.g. Getting Started with Flat-File Architecture"
              value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
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
              placeholder="Write your article in Markdown here..."
              required
            ><?= htmlspecialchars((string) ($old['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Right Publishing Sidebar -->
      <div style="width: 320px; max-width: 100%;">
        <div class="card mb-4" style="padding: 1.5rem;">
          <h3 class="mb-3" style="font-size: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="send" style="color: var(--primary);"></i>
            <span>Publishing Options</span>
          </h3>

          <div class="field">
            <label class="label" for="postStatus">Status</label>
            <select id="postStatus" class="input" name="status">
              <option value="published" <?= ($old['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>
                🟢 Published (Public)
              </option>
              <option value="draft" <?= ($old['status'] ?? '') === 'draft' ? 'selected' : '' ?>>
                🟡 Draft (Private)
              </option>
            </select>
            <div class="help">Drafts are hidden from public visitors but visible to logged-in admins.</div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1.5rem;">
            <button type="submit" class="button button-primary button-block button-lg">
              <i data-lucide="check"></i> Publish Article
            </button>
            <a href="/admin/media" target="_blank" rel="noopener" class="button button-light button-block">
              <i data-lucide="image"></i> Media Library
            </a>
            <a href="/admin" class="button button-light button-block text-muted">
              Cancel
            </a>
          </div>
        </div>
      </div>

    </div>
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
