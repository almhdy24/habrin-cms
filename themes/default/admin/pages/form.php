<?php 
$pageTitle = $page ? __('pages.edit_page') : __('pages.create_page');
$this->layout('layouts/admin', ['title' => $pageTitle]);
$pageId = $page ? (string) ($page['slug'] ?? 'edit') : 'new';
?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="file-text"></i> <?= $pageTitle ?></h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;"><?= __('dashboard.tagline') ?></p>
    </div>
    <div class="flex gap-1">
      <a href="/admin/pages" class="button button-light">
        <i data-lucide="arrow-left"></i> <?= __('pages.title') ?>
      </a>
      <?php if ($page && !empty($page['slug'])): ?>
        <a href="/<?= htmlspecialchars($page['slug'], ENT_QUOTES, 'UTF-8') ?>" class="button button-light" target="_blank" rel="noopener">
          <i data-lucide="external-link"></i> <?= __('pages.view_page') ?>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <form method="POST" class="card" id="pageForm">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <div class="field">
      <label class="label" for="pageTitle"><i data-lucide="type"></i> <?= __('pages.page_title') ?> <span class="text-muted" style="font-size: 0.85rem; font-weight: 400;">(<?= __('common.required') ?>)</span></label>
      <input
        id="pageTitle"
        class="input"
        type="text"
        name="title"
        placeholder="<?= __('pages.page_title') ?>"
        value="<?= htmlspecialchars((string) ($page['title'] ?? '')) ?>"
        required
        autofocus
        style="font-size: 1.15rem; font-weight: 600;"
      >
    </div>

    <?php if (!$page): ?>
      <div class="field">
        <label class="label" for="pageSlug"><i data-lucide="link"></i> <?= __('pages.page_slug') ?> <span class="text-muted" style="font-size: 0.85rem; font-weight: 400;">(<?= __('common.required') ?>)</span></label>
        <input
          id="pageSlug"
          class="input"
          type="text"
          name="slug"
          placeholder="about"
          required
          style="font-family: var(--font-mono); font-size: 0.9rem;"
        >
        <p class="field-hint"><i data-lucide="info"></i> URL: <code>/<span id="pageSlugPreview">about</span></code></p>
      </div>
    <?php endif; ?>

    <div class="field mb-3">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
        <label class="label mb-0" for="pageContent"><i data-lucide="align-left"></i> <?= __('pages.page_content') ?></label>
        <div class="text-muted" style="font-size: 0.82rem; display: flex; gap: 0.85rem;">
          <span><i data-lucide="file-text" style="width:14px;height:14px;display:inline;"></i> <strong id="wordCount">0</strong> <?= __('posts.words') ?></span>
          <span>Markdown</span>
        </div>
      </div>
      <textarea
        id="pageContent"
        class="textarea"
        name="content"
        rows="16"
        placeholder="<?= __('pages.page_content') ?>..."
        required
      ><?= htmlspecialchars((string) ($page['content'] ?? '')) ?></textarea>
    </div>

    <div class="flex flex-wrap gap-1 mt-4">
      <button type="submit" class="button button-primary">
        <i data-lucide="save"></i> <?= __('pages.save_page') ?>
      </button>
      <button type="button" class="button button-light" id="btnInsertMedia">
        <i data-lucide="image"></i> <?= __('posts.insert_media') ?>
      </button>
      <a href="/admin/pages" class="button button-light">
        <i data-lucide="x"></i> <?= __('common.close') ?>
      </a>
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
  const textarea = document.getElementById('pageContent');
  const counter = document.getElementById('wordCount');
  const pageForm = document.getElementById('pageForm');
  if (!textarea || typeof EasyMDE === 'undefined') return;

  const autosaveKey = 'habrin_page_<?= $pageId ?>';

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

  if (pageForm) {
    pageForm.addEventListener('submit', function () {
      if (editor && typeof editor.clearAutosavedValue === 'function') {
        editor.clearAutosavedValue();
      }
      try {
        localStorage.removeItem('smde_' + autosaveKey);
      } catch (e) {}
    });
  }

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
});
</script>
