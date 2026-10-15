<?php $this->layout('layouts/admin', ['title' => $page ? 'Edit Page' : 'Create Page']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="file-text"></i> <?= $page ? 'Edit Page' : 'Create Page' ?></h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;">Standalone static page with Markdown support.</p>
    </div>
    <div class="flex gap-1">
      <a href="/admin/pages" class="button button-light">
        <i data-lucide="arrow-left"></i> All Pages
      </a>
      <?php if ($page && !empty($page['slug'])): ?>
        <a href="/<?= htmlspecialchars($page['slug'], ENT_QUOTES, 'UTF-8') ?>" class="button button-light" target="_blank" rel="noopener">
          <i data-lucide="external-link"></i> View Page
        </a>
      <?php endif; ?>
    </div>
  </div>

  <form method="POST" class="card">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <div class="field">
      <label class="label" for="pageTitle"><i data-lucide="type"></i> Page Title <span class="text-muted" style="font-size: 0.85rem; font-weight: 400;">(required)</span></label>
      <input
        id="pageTitle"
        class="input"
        type="text"
        name="title"
        placeholder="e.g. About Our Publication"
        value="<?= htmlspecialchars((string) ($page['title'] ?? '')) ?>"
        required
        autofocus
        style="font-size: 1.15rem; font-weight: 600;"
      >
      <p class="field-hint"><i data-lucide="info"></i> The public title displayed at the top of the standalone page.</p>
    </div>

    <?php if (!$page): ?>
      <div class="field">
        <label class="label" for="pageSlug"><i data-lucide="link"></i> Page Slug (URL) <span class="text-muted" style="font-size: 0.85rem; font-weight: 400;">(required)</span></label>
        <input
          id="pageSlug"
          class="input"
          type="text"
          name="slug"
          placeholder="e.g. about"
          required
          style="font-family: var(--font-mono); font-size: 0.9rem;"
        >
        <p class="field-hint"><i data-lucide="info"></i> Unique path where this page will be served: <code>/<span id="pageSlugPreview">about</span></code></p>
      </div>
    <?php endif; ?>

    <div class="field mb-3">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
        <label class="label mb-0" for="pageContent"><i data-lucide="align-left"></i> Content</label>
        <div class="text-muted" style="font-size: 0.82rem; display: flex; gap: 0.85rem;">
          <span><i data-lucide="file-text" style="width:14px;height:14px;display:inline;"></i> <strong id="wordCount">0</strong> words</span>
          <span>Markdown Supported</span>
        </div>
      </div>
      <textarea
        id="pageContent"
        class="textarea"
        name="content"
        rows="16"
        placeholder="Write the page body in Markdown..."
        required
      ><?= htmlspecialchars((string) ($page['content'] ?? '')) ?></textarea>
    </div>

    <div class="flex flex-wrap gap-1 mt-4">
      <button type="submit" class="button button-primary">
        <i data-lucide="save"></i> Save Page
      </button>
      <button type="button" class="button button-light" id="btnInsertMedia">
        <i data-lucide="image"></i> Insert Media
      </button>
      <a href="/admin/pages" class="button button-light">
        <i data-lucide="x"></i> Cancel
      </a>
    </div>
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
  const textarea = document.getElementById('pageContent');
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

  // Slug generator if on create page
  const titleInput = document.getElementById('pageTitle');
  const slugInput = document.getElementById('pageSlug');
  const slugPreview = document.getElementById('pageSlugPreview');

  if (titleInput && slugInput && slugPreview) {
    let userEditedSlug = false;
    function toSlug(str) {
      return str.toString().toLowerCase().trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    }

    slugInput.addEventListener('input', function() {
      userEditedSlug = Boolean(slugInput.value.trim());
      slugPreview.textContent = userEditedSlug ? toSlug(slugInput.value) : 'about';
    });

    titleInput.addEventListener('input', function() {
      if (!userEditedSlug) {
        const gen = toSlug(titleInput.value);
        slugInput.value = gen;
        slugPreview.textContent = gen || 'about';
      }
    });
  }

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
});
</script>
