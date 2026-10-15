<?php $this->layout('layouts/admin', ['title' => __('posts.edit_title')]) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="edit-3"></i> <?= __('posts.edit_title') ?></h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;">
        #<?= (int) $id ?> · <?= date('M j, Y', (int) $post['created_at']) ?>
        <?php if (!empty($post['updated_at']) && $post['updated_at'] !== $post['created_at']): ?>
          · <?= date('M j, Y', (int) $post['updated_at']) ?>
        <?php endif; ?>
      </p>
    </div>
    <div class="flex gap-1">
      <a href="/admin" class="button button-light">
        <i data-lucide="arrow-left"></i> <?= __('posts.title') ?>
      </a>
      <a href="/post/<?= htmlspecialchars($post['slug'] ?? '') ?>" class="button button-light" target="_blank" rel="noopener">
        <i data-lucide="eye"></i> <?= __('common.preview') ?>
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
              <?= __('posts.article_title') ?> <span class="text-muted" style="font-size: 0.85rem; font-weight: 400;">(<?= __('common.required') ?>)</span>
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
              required
            ><?= htmlspecialchars((string) ($old['content'] ?? $post['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Right Publishing Sidebar -->
      <div id="postEditorSidebar" class="post-editor-sidebar" style="width: 320px; max-width: 100%;">
        <div class="card mb-3" style="padding: 1.5rem;">
          <h3 class="mb-3" style="font-size: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="send" style="color: var(--primary);"></i>
            <span><?= __('posts.publishing_options') ?></span>
          </h3>

          <div class="field">
            <label class="label" for="postStatus"><?= __('common.status') ?></label>
            <?php $currentStatus = $old['status'] ?? $post['status'] ?? 'published'; ?>
            <select id="postStatus" class="input" name="status">
              <option value="published" <?= $currentStatus === 'published' ? 'selected' : '' ?>>
                <?= __('posts.status_published') ?>
              </option>
              <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>
                <?= __('posts.status_draft') ?>
              </option>
            </select>
          </div>

          <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1.5rem;">
            <button type="submit" class="button button-primary button-block button-lg">
              <i data-lucide="save"></i> <?= __('posts.edit_title') ?>
            </button>
            <a href="/post/<?= htmlspecialchars((string) ($post['slug'] ?? '')) ?>" class="button button-light button-block" target="_blank" rel="noopener">
              <i data-lucide="external-link"></i> <?= __('common.preview') ?>
            </a>
            <button type="button" class="button button-light button-block" id="btnInsertMedia">
              <i data-lucide="image"></i> <?= __('posts.insert_media') ?>
            </button>
          </div>
        </div>

        <!-- Article Metadata Card -->
        <div class="card mb-3" style="padding: 1.5rem;">
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
              value="<?= htmlspecialchars((string) ($old['slug'] ?? $post['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
              style="font-family: var(--font-mono); font-size: 0.85rem;"
            >
            <div class="help mt-1" style="font-size: 0.78rem; word-break: break-all;">
              URL: <code style="font-size: 0.78rem; color: var(--primary);">/post/<span id="slugPreview"><?= htmlspecialchars((string) ($old['slug'] ?? $post['slug'] ?? '')) ?></span></code>
            </div>
          </div>

          <div class="field">
            <label class="label" for="postTags"><?= __('posts.tags') ?></label>
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
              placeholder="tech, tutorial"
              value="<?= htmlspecialchars($tagsVal, ENT_QUOTES, 'UTF-8') ?>"
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
            ><?= htmlspecialchars((string) ($old['excerpt'] ?? $post['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            <div class="help"><?= __('posts.excerpt_hint') ?></div>
          </div>
        </div>

        <!-- Move to Trash Card -->
        <div class="card" style="padding: 1.25rem; border-color: rgba(239, 68, 68, 0.25);">
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
            <span class="text-muted" style="font-size: 0.85rem;"><?= __('posts.move_to_trash') ?>:</span>
            <button type="submit" form="deletePostForm" class="button button-sm button-danger" onclick="return confirm('<?= __('common.confirm_delete') ?>')">
              <i data-lucide="trash-2"></i> <?= __('posts.move_to_trash') ?>
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

  const postId = <?= (int) $id ?>;
  const autosaveKey = 'habrin_draft_post_' + postId;

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
