<?php $this->layout('layouts/admin', ['title' => 'Edit Post']) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-1 mb-4">
    <div>
      <h2 class="mb-0"><i data-lucide="file-edit"></i> Edit Post</h2>
      <p class="text-muted" style="margin-top:0.25rem;font-size:0.9rem;">
        Post #<?= (int) $id ?> · <?= date('F j, Y', (int) $post['created_at']) ?>
        <?php if (isset($post['updated_at']) && $post['updated_at'] != $post['created_at']): ?>
          (updated <?= date('M j, Y', (int) $post['updated_at']) ?>)
        <?php endif; ?>
      </p>
    </div>
    <div class="flex flex-wrap gap-1">
      <a href="/admin" class="button button-light">
        <i data-lucide="arrow-left"></i> Back
      </a>
      <a href="/post/<?= htmlspecialchars($post['slug']) ?>" class="button button-primary" target="_blank" rel="noopener">
        <i data-lucide="eye"></i> View Post
      </a>
    </div>
  </div>

  <!-- Post metadata card -->
  <div class="card mb-4" style="background:var(--bg-soft);border:1px solid var(--border);padding:1rem 1.5rem;">
    <div class="flex flex-wrap gap-3" style="font-size:0.9rem;">
      <span><strong>ID:</strong> #<?= (int) $id ?></span>
      <span><strong>Slug:</strong> <code><?= htmlspecialchars($post['slug']) ?></code></span>
      <span><strong>Created:</strong> <?= date('M j, Y g:i A', (int) $post['created_at']) ?></span>
      <?php if (isset($post['updated_at']) && $post['updated_at'] != $post['created_at']): ?>
        <span><strong>Updated:</strong> <?= date('M j, Y g:i A', (int) $post['updated_at']) ?></span>
      <?php endif; ?>
      <span><strong>Status:</strong> <?= ($post['status'] ?? 'published') === 'draft' ? '<span style="color:#856404;font-weight:600;">Draft</span>' : '<span style="color:#28a745;font-weight:600;">Published</span>' ?></span>
    </div>
  </div>

  <form method="POST" action="/admin/edit/<?= (int) $id ?>" class="card" id="postForm" style="padding:1.5rem;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <!-- TITLE -->
    <div class="field">
      <label class="label" for="title"><i data-lucide="type"></i> Title <span class="text-muted">(required)</span></label>
      <input id="title" class="input" type="text" name="title" value="<?= htmlspecialchars($old['title'] ?? $post['title']) ?>" required autofocus>
      <div class="help"><i data-lucide="info"></i> This will be used as the post headline.</div>
    </div>

    <!-- STATUS & PUBLISHING -->
    <div class="field">
      <label class="label" for="status"><i data-lucide="check-circle"></i> Publication Status</label>
      <select id="status" class="input" name="status" style="max-width:220px;">
        <option value="published" <?= ($post['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published (Live)</option>
        <option value="draft" <?= ($post['status'] ?? 'published') === 'draft' ? 'selected' : '' ?>>Draft (Unpublished)</option>
      </select>
      <div class="help">Drafts are saved safely but not shown to public visitors.</div>
    </div>

    <!-- CONTENT -->
    <div class="field">
      <label class="label" for="content"><i data-lucide="align-left"></i> Content <span class="text-muted">(required)</span></label>
      <textarea id="content" class="textarea simple-editor" name="content" rows="15" required><?= htmlspecialchars($old['content'] ?? $post['content']) ?></textarea>
      <div class="help" style="display:flex;gap:1rem;flex-wrap:wrap;">
        <span><i data-lucide="info"></i> Supports <a href="https://commonmark.org/help/" target="_blank" rel="noopener">Markdown</a></span>
        <span><i data-lucide="type"></i> <span id="wordCount">0</span> words</span>
      </div>
    </div>

    <!-- ACTIONS -->
    <div class="flex flex-wrap items-center justify-between gap-2 mt-4" style="padding-top:1rem;border-top:1px solid var(--border);">
      <div class="flex flex-wrap gap-1">
        <button type="submit" class="button button-primary"><i data-lucide="save"></i> Update Post</button>
        <button type="reset" class="button button-light"><i data-lucide="rotate-ccw"></i> Reset</button>
        <a href="/admin" class="button button-light"><i data-lucide="x"></i> Cancel</a>
      </div>
      <a href="/admin/media" target="_blank" rel="noopener" class="button button-light">
        <i data-lucide="image"></i> Open Media Library
      </a>
    </div>

  </form>

  <div class="card mt-3" style="padding:1rem 1.5rem;background:var(--bg-soft);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
    <span class="text-muted" style="font-size:0.9rem;">Move this post to the trash bin:</span>
    <form method="POST" action="/admin/delete/<?= (int) $id ?>" onsubmit="return confirm('Move this post to trash?')">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="button button-danger"><i data-lucide="trash-2"></i> Move to Trash</button>
    </form>
  </div>
</section>

<!-- EasyMDE -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const textarea = document.getElementById('content');
  const counter = document.getElementById('wordCount');
  if (!textarea || typeof EasyMDE === 'undefined') return;

  const editor = new EasyMDE({
    element: textarea,
    spellChecker: false,
    forceSync: true,
    status: false,
    autofocus: true,
    toolbar: [
      "bold", "italic", "heading", "|",
      "unordered-list", "ordered-list", "|",
      "link", "image", "|",
      "preview", "fullscreen"
    ]
  });

  const updateCount = () => {
    const text = editor.value().trim();
    const words = text ? text.split(/\s+/).length : 0;
    counter.textContent = words;
  };

  editor.codemirror.on('change', updateCount);
  updateCount();
});
</script>
