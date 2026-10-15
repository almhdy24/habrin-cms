<?php $this->layout('layouts/admin', ['title' => __('help.title')]) ?>

<section>
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <div>
      <h2 class="mb-1"><i data-lucide="book-open"></i> <?= __('help.title') ?></h2>
      <p class="text-muted" style="margin: 0; font-size: 0.9rem;">
        Comprehensive guides, tutorials, and technical reference for Habrin CMS.
      </p>
    </div>
    <a href="/admin/dashboard" class="button button-light">
      <i data-lucide="arrow-left"></i> <?= __('nav.dashboard') ?>
    </a>
  </div>

  <!-- Search & Filter Bar -->
  <div class="card mb-4" style="padding: 1.5rem; background: var(--bg-soft); border-color: var(--border);">
    <form method="GET" action="/admin/help" class="flex flex-wrap items-center gap-2">
      <div style="flex: 1; min-width: 260px; position: relative;">
        <input
          type="search"
          id="helpSearchInput"
          name="q"
          class="input"
          placeholder="<?= __('help.search_placeholder') ?>"
          value="<?= htmlspecialchars((string) ($query ?? ''), ENT_QUOTES, 'UTF-8') ?>"
          style="padding: 0.65rem 1rem; font-size: 1rem; background: var(--white);"
        >
      </div>
      <button type="submit" class="button button-primary">
        <i data-lucide="search"></i> <?= __('common.search') ?>
      </button>
      <?php if (!empty($query)): ?>
        <a href="/admin/help" class="button button-light">
          <i data-lucide="x"></i> <?= __('common.all') ?>
        </a>
      <?php endif; ?>
    </form>
    <div id="helpMatchCount" class="text-muted mt-2" style="font-size: 0.85rem;"></div>
  </div>

  <!-- Guides Grid -->
  <?php if (empty($guides)): ?>
    <div class="card text-center" style="padding: 3.5rem 1rem;">
      <i data-lucide="search" style="width: 48px; height: 48px; color: var(--text-muted); margin: 0 auto 1rem;"></i>
      <h3>No guides matched your search</h3>
      <p class="text-muted">Try using different keywords or view all documentation topics.</p>
      <a href="/admin/help" class="button button-primary mt-2">View All Documentation</a>
    </div>
  <?php else: ?>
    <div id="guidesContainer" style="display: flex; flex-direction: column; gap: 1.25rem;">
      <?php foreach ($guides as $guide): ?>
        <article class="card guide-card" data-search="<?= strtolower(htmlspecialchars($guide['title'] . ' ' . $guide['summary'] . ' ' . strip_tags($guide['content']), ENT_QUOTES, 'UTF-8')) ?>" style="padding: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(49, 92, 82, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="<?= htmlspecialchars($guide['icon']) ?>"></i>
              </div>
              <div>
                <h3 style="margin: 0; font-size: 1.15rem;"><?= htmlspecialchars($guide['title']) ?></h3>
                <span class="badge" style="background: var(--bg-soft); color: var(--text-muted); font-size: 0.78rem; font-weight: 600; text-transform: uppercase; margin-top: 2px;">
                  <?= htmlspecialchars($guide['category']) ?>
                </span>
              </div>
            </div>
          </div>

          <p class="text-muted" style="margin: 0 0 1rem 0; font-size: 0.95rem; line-height: 1.5;">
            <?= htmlspecialchars($guide['summary']) ?>
          </p>

          <details style="border-top: 1px solid var(--border); padding-top: 0.85rem;">
            <summary style="cursor: pointer; font-weight: 700; color: var(--primary); font-size: 0.9rem; user-select: none;">
              Read Full Documentation &rarr;
            </summary>
            <div class="guide-body typography mt-3" style="font-size: 0.95rem; line-height: 1.7; padding-top: 0.5rem;">
              <?= $guide['content'] ?>
            </div>
          </details>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Instant Search Script -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('helpSearchInput');
    const cards = document.querySelectorAll('.guide-card');
    const counter = document.getElementById('helpMatchCount');
    if (!searchInput || !cards.length) return;

    searchInput.addEventListener('input', function() {
      const q = searchInput.value.toLowerCase().trim();
      let matchCount = 0;

      cards.forEach(card => {
        const text = card.getAttribute('data-search') || '';
        if (!q || text.includes(q)) {
          card.style.display = 'block';
          matchCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (counter) {
        counter.textContent = q ? `Showing ${matchCount} of ${cards.length} documentation topics` : '';
      }
    });
  });
  </script>
</section>
