<?php $this->layout('layouts/admin', ['title' => __('posts.write_new')]) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="file-plus"></i> <?= __('posts.write_new') ?></h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;"><?= __('dashboard.tagline') ?></p>
    </div>
    <a href="/admin" class="button button-light">
      <i data-lucide="arrow-left"></i> <?= __('posts.title') ?>
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
              <?= __('posts.article_title') ?> <span class="text-muted" style="font-size: 0.85rem; font-weight: 400;">(<?= __('common.required') ?>)</span>
            </label>
            <input
              id="postTitle"
              class="input"
              type="text"
              name="title"
              placeholder="<?= __('posts.article_title') ?>"
              value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
              required
              autofocus
              style="font-size: 1.25rem; font-weight: 700; padding: 0.85rem 1rem;"
            >
          </div>

          <div class="field mb-0">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
              <label class="label mb-0" for="content"><?= __('posts.content') ?></label>
              <div class="text-muted" style="font-size: 0.82rem; display: flex; gap: 0.85rem;">
                <span><i data-lucide="file-text" style="width:14px;height:14px;display:inline;"></i> <strong id="wordCount">0</strong> <?= __('posts.words') ?></span>
                <span>Markdown</span>
              </div>
            </div>
            <textarea
              id="content"
              class="textarea simple-editor"
              name="content"
              rows="16"
              placeholder="<?= __('posts.content') ?>..."
              required
            ><?= htmlspecialchars((string) ($old['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Right Publishing Sidebar -->
      <div id="postEditorSidebar" class="post-editor-sidebar" style="width: 320px; max-width: 100%;">
        <div class="card mb-4" style="padding: 1.5rem;">
          <h3 class="mb-3" style="font-size: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="send" style="color: var(--primary);"></i>
            <span><?= __('posts.publishing_options') ?></span>
          </h3>

          <div class="field">
            <label class="label" for="postStatus"><?= __('common.status') ?></label>
            <select id="postStatus" class="input" name="status">
              <option value="published" <?= ($old['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>
                <?= __('posts.status_published') ?>
              </option>
              <option value="draft" <?= ($old['status'] ?? '') === 'draft' ? 'selected' : '' ?>>
                <?= __('posts.status_draft') ?>
              </option>
            </select>
          </div>

          <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1.5rem;">
            <button type="submit" class="button button-primary button-block button-lg">
              <i data-lucide="check"></i> <?= __('posts.write_new') ?>
            </button>
            <button type="button" class="button button-light button-block" id="btnInsertMedia">
              <i data-lucide="image"></i> <?= __('posts.insert_media') ?>
            </button>
            <a href="/admin" class="button button-light button-block text-muted">
              <?= __('common.close') ?>
            </a>
          </div>
        </div>

        <div class="card mb-4" style="padding: 1.5rem;">
          <h3 class="mb-3" style="font-size: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="sliders" style="color: var(--primary);"></i>
            <span><?= __('posts.publishing_options') ?></span>
          </h3>

          <div class="field">
            <label class="label" for="postSlug"><?= __('posts.url_slug') ?></label>
            <input
              id="postSlug"
              class="input"
              type="text"
              name="slug"
              placeholder="auto-generated-slug"
              value="<?= htmlspecialchars((string) ($old['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
              style="font-family: var(--font-mono); font-size: 0.85rem;"
            >
            <div class="help mt-1" style="font-size: 0.78rem; word-break: break-all;">
              URL: <code style="font-size: 0.78rem; color: var(--primary);">/post/<span id="slugPreview"><?= htmlspecialchars((string) ($old['slug'] ?? 'your-slug'), ENT_QUOTES, 'UTF-8') ?></span></code>
            </div>
          </div>

          <div class="field">
            <label class="label" for="postTags"><?= __('posts.tags') ?></label>
            <input
              id="postTags"
              class="input"
              type="text"
              name="tags"
              placeholder="news, tutorials"
              value="<?= htmlspecialchars((string) ($old['tags'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            >
            <div class="help"><?= __('posts.tags_hint') ?></div>
          </div>

          <div class="field mb-0">
            <label class="label" for="postExcerpt"><?= __('posts.excerpt') ?></label>
            <textarea
              id="postExcerpt"
              class="textarea"
              name="excerpt"
              rows="3"
              placeholder="<?= __('posts.excerpt') ?>..."
            ><?= htmlspecialchars((string) ($old['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            <div class="help"><?= __('posts.excerpt_hint') ?></div>
          </div>
        </div>
      </div>

    </div>
  </form>
</section>

<!-- Media Selection Modal -->
<dialog id="mediaModal" style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; max-width: 640px; width: 90vw; background: var(--white); box-shadow: var(--shadow-lg);">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
    <h3 style="margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
      <i data-lucide="image"></i> <span><?= __('media.title') ?></span>
    </h3>
    <button type="button" id="closeMediaModalBtn" class="button button-sm button-light" style="padding: 0.25rem 0.5rem;">✕</button>
  </div>
  <div id="mediaModalList" style="max-height: 360px; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.75rem; padding: 0.25rem;">
    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 1rem;"><?= __('common.loading') ?></div>
  </div>
  <div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 0.75rem;">
    <a href="/admin/media" target="_blank" rel="noopener" class="button button-sm button-light">
      <i data-lucide="upload"></i> <?= __('media.upload_title') ?>
    </a>
    <button type="button" id="cancelMediaModalBtn" class="button button-sm button-light"><?= __('common.close') ?></button>
  </div>
</dialog>

<!-- EasyMDE Editor Integration -->
<link rel="stylesheet" href="<?= $this->asset('assets/easymde.min.css') ?>">
<script src="<?= $this->asset('assets/easymde.min.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const textarea = document.getElementById('content');
  const counter = document.getElementById('wordCount');
  const postForm = document.getElementById('postForm');
  if (!textarea || typeof EasyMDE === 'undefined') return;

  const autosaveKey = 'habrin_draft_new_post';

  const editor = new EasyMDE({
    element: textarea,
    spellChecker: false,
    forceSync: true,
    status: false,
    autosave: {
      enabled: true,
      uniqueId: autosaveKey,
      delay: 2000
    },
    toolbar: [
      "bold", "italic", "heading", "|",
      "quote", "unordered-list", "ordered-list", "|",
      "link", {
        name: "insertMedia",
        action: function() { openMediaModal(); },
        className: "fa fa-picture-o",
        title: "<?= __('posts.insert_media') ?>"
      }, "code", "table", "|",
      "preview", "side-by-side", "fullscreen"
    ]
  });

  const updateCount = () => {
    const text = editor.value().trim();
    const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
    if (counter) counter.textContent = words;
  };

  editor.codemirror.on('change', updateCount);
  updateCount();

  // On form submit, clear autosave
  if (postForm) {
    postForm.addEventListener('submit', function () {
      if (editor && typeof editor.clearAutosavedValue === 'function') {
        editor.clearAutosavedValue();
      }
      try {
        localStorage.removeItem('smde_' + autosaveKey);
      } catch (e) {}
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
    listContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 1.5rem;"><?= __('common.loading') ?></div>';
    fetch('/admin/media/json')
      .then(res => res.json())
      .then(data => {
        if (!data.items || data.items.length === 0) {
          listContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 1.5rem;"><?= __('media.empty_library') ?></div>';
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
            <button type="button" class="button button-sm button-primary button-block" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><?= __('posts.insert_media') ?></button>
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

  // Slug auto-generation & preview
  const titleInput = document.getElementById('postTitle');
  const slugInput = document.getElementById('postSlug');
  const slugPreview = document.getElementById('slugPreview');

  function toSlug(str) {
    return str.toString().toLowerCase().trim()
      .replace(/[^\w\s-]/g, '')
      .replace(/[\s_-]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  if (slugInput && slugPreview) {
    let userEditedSlug = Boolean(slugInput.value.trim());

    slugInput.addEventListener('input', function () {
      userEditedSlug = Boolean(slugInput.value.trim());
      slugPreview.textContent = userEditedSlug ? toSlug(slugInput.value) : 'your-slug';
    });

    if (titleInput) {
      titleInput.addEventListener('input', function () {
        if (!userEditedSlug) {
          const generated = toSlug(titleInput.value);
          slugInput.value = generated;
          slugPreview.textContent = generated || 'your-slug';
        }
      });
    }
  }
});
</script>
