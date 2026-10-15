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
            <?php $currentStatus = $old['status'] ?? $post['status'] ?? 'published'; ?>
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

          <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1.5rem;">
            <button type="submit" class="button button-primary button-block button-lg">
              <i data-lucide="save"></i> Update Article
            </button>
            <a href="/post/<?= htmlspecialchars((string) ($post['slug'] ?? '')) ?>" class="button button-light button-block" target="_blank" rel="noopener">
              <i data-lucide="external-link"></i> View Live Article
            </a>
            <button type="button" class="button button-light button-block" id="btnInsertMedia">
              <i data-lucide="image"></i> Insert Media
            </button>
          </div>
        </div>

        <!-- Article Metadata Card -->
        <div class="card mb-3" style="padding: 1.5rem;">
          <h3 class="mb-3" style="font-size: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="sliders" style="color: var(--primary);"></i>
            <span>Article Metadata</span>
          </h3>

          <div class="field">
            <label class="label" for="postSlug">URL Slug</label>
            <input
              id="postSlug"
              class="input"
              type="text"
              name="slug"
              value="<?= htmlspecialchars((string) ($old['slug'] ?? $post['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
              style="font-family: var(--font-mono); font-size: 0.85rem;"
            >
            <div class="help mt-1" style="font-size: 0.78rem; word-break: break-all;">
              URL: <code style="font-size: 0.78rem; color: var(--primary);">/post/<span id="slugPreview"><?= htmlspecialchars((string) ($old['slug'] ?? $post['slug'] ?? '')) ?></span></code>
            </div>
            <div class="help text-muted" style="font-size: 0.78rem;">Keep unchanged to preserve existing inbound links.</div>
          </div>

          <div class="field">
            <label class="label" for="postTags">Tags</label>
            <?php
              $tagsVal = isset($old['tags'])
                ? (string) $old['tags']
                : (is_array($post['tags'] ?? null) ? implode(', ', $post['tags']) : (string) ($post['tags'] ?? ''));
            ?>
            <input
              id="postTags"
              class="input"
              type="text"
              name="tags"
              placeholder="tech, tutorial, tips"
              value="<?= htmlspecialchars($tagsVal, ENT_QUOTES, 'UTF-8') ?>"
            >
            <div class="help">Comma-separated tags for categorization.</div>
          </div>

          <div class="field mb-0">
            <label class="label" for="postExcerpt">Summary / Excerpt</label>
            <textarea
              id="postExcerpt"
              class="textarea"
              name="excerpt"
              rows="3"
              placeholder="Brief summary for listings and SEO..."
            ><?= htmlspecialchars((string) ($old['excerpt'] ?? $post['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            <div class="help">Used as post preview snippet and meta description.</div>
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

<!-- Media Selection Modal -->
<dialog id="mediaModal" style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; max-width: 640px; width: 90vw; background: var(--white); box-shadow: var(--shadow-lg);">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
    <h3 style="margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="image"></i> <span>Media Library</span>
    </h3>
    <button type="button" id="closeMediaModalBtn" class="button button-sm button-light" style="padding: 0.25rem 0.5rem;">✕</button>
  </div>
  <div id="mediaModalList" style="max-height: 360px; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.75rem; padding: 0.25rem;">
    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 1rem;">Loading media...</div>
  </div>
  <div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 0.75rem;">
    <a href="/admin/media" target="_blank" rel="noopener" class="button button-sm button-light">
      <i data-lucide="upload"></i> Upload More
    </a>
    <button type="button" id="cancelMediaModalBtn" class="button button-sm button-light">Close</button>
  </div>
</dialog>

<!-- EasyMDE Editor Integration -->
<link rel="stylesheet" href="<?= $this->asset('assets/easymde.min.css') ?>">
<script src="<?= $this->asset('assets/easymde.min.js') ?>"></script>

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
      "link", {
        name: "insertMedia",
        action: function() { openMediaModal(); },
        className: "fa fa-picture-o",
        title: "Insert Media from Library"
      }, "code", "table", "|",
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

  // Media Modal handling
  const modal = document.getElementById('mediaModal');
  const openBtn = document.getElementById('btnInsertMedia');
  const closeBtn = document.getElementById('closeMediaModalBtn');
  const cancelBtn = document.getElementById('cancelMediaModalBtn');
  const listContainer = document.getElementById('mediaModalList');

  function openMediaModal() {
    if (!modal) return;
    if (typeof modal.showModal === 'function') {
      modal.showModal();
    } else {
      modal.style.display = 'block';
    }
    loadMedia();
  }

  function closeMediaModal() {
    if (!modal) return;
    if (typeof modal.close === 'function') {
      modal.close();
    } else {
      modal.style.display = 'none';
    }
  }

  if (openBtn) openBtn.addEventListener('click', openMediaModal);
  if (closeBtn) closeBtn.addEventListener('click', closeMediaModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeMediaModal);

  function loadMedia() {
    listContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 1.5rem;">Loading media files...</div>';
    fetch('/admin/media/json')
      .then(res => res.json())
      .then(data => {
        if (!data.items || data.items.length === 0) {
          listContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 1.5rem;">No media items found. Upload images in the Media Library.</div>';
          return;
        }

        listContainer.innerHTML = '';
        data.items.forEach(item => {
          const card = document.createElement('div');
          card.style.cssText = 'border: 1px solid var(--border); border-radius: 6px; padding: 0.5rem; text-align: center; background: var(--paper); display: flex; flex-direction: column; justify-content: space-between;';
          
          let previewHtml = '';
          if (item.is_image) {
            previewHtml = `<div style="height: 80px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 0.5rem;"><img src="${item.url}" alt="${item.name}" style="max-height: 80px; max-width: 100%; object-fit: cover; border-radius: 4px;"></div>`;
          } else {
            previewHtml = `<div style="height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem; font-weight: bold; color: var(--text-muted);">📄</div>`;
          }

          card.innerHTML = `
            ${previewHtml}
            <div style="font-size: 0.75rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 0.5rem;" title="${item.name}">${item.name}</div>
            <button type="button" class="button button-sm button-primary button-block" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Insert</button>
          `;

          const insertBtn = card.querySelector('button');
          insertBtn.addEventListener('click', () => {
            const snippet = item.is_image ? `![${item.name}](${item.url})` : `[${item.name}](${item.url})`;
            editor.codemirror.replaceSelection(snippet);
            closeMediaModal();
            if (window.habrinToast) {
              window.habrinToast('Media inserted into editor', 'success');
            }
          });

          listContainer.appendChild(card);
        });
      })
      .catch(err => {
        listContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; color: var(--danger); padding: 1.5rem;">Failed to load media items.</div>';
      });
  }

  // Slug preview handler
  const slugInput = document.getElementById('postSlug');
  const slugPreview = document.getElementById('slugPreview');

  if (slugInput && slugPreview) {
    slugInput.addEventListener('input', function () {
      const cleaned = slugInput.value.toLowerCase().trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
      slugPreview.textContent = cleaned || 'your-slug';
    });
  }
});
</script>
