<?php $this->layout('layouts/site', ['title' => ($settings['site_title'] ?? 'The Habrin Chronicle') . ' — Front Page']) ?>

<section class="front-page">
  <?php if (empty($posts)): ?>
    <div class="empty-newsroom">
      <h2>The Front Page is Currently Silent</h2>
      <p class="text-muted">No news stories or editorial dispatches have been filed today.</p>
      <?php if ($this->is_admin()): ?>
        <p class="mt-3"><a href="/admin/add" class="button-editorial"><i data-lucide="edit-3"></i> File First Story</a></p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <?php
    $postList = array_values($posts);
    $leadStory = $postList[0];
    $remainingStories = array_slice($postList, 1);
    ?>

    <!-- Lead Headline Story -->
    <article class="lead-story">
      <div class="lead-badge">Leading Dispatch</div>
      <h2 class="lead-headline">
        <a href="/post/<?= $this->e($leadStory['slug']) ?>"><?= $this->e($leadStory['title']) ?></a>
      </h2>
      <div class="story-byline">
        <span class="byline-date"><?= date('F j, Y', (int) $leadStory['created_at']) ?></span>
        <span class="byline-dot">&bull;</span>
        <span class="byline-desk">Chronicle Editorial Bureau</span>
        <?php if ($this->is_admin()): ?>
          <span class="byline-dot">&bull;</span>
          <a href="/admin/edit/<?= (int) ($leadStory['id'] ?? 0) ?>" class="edit-link">Edit Story</a>
        <?php endif; ?>
      </div>
      <div class="lead-lead">
        <?php
        $leadRendered = $this->filter('post_content', $leadStory['content'], $leadStory);
        $leadPlain = trim(strip_tags($leadRendered));
        echo htmlspecialchars(mb_substr($leadPlain, 0, 320)) . (mb_strlen($leadPlain) > 320 ? '…' : '');
        ?>
      </div>
      <div class="lead-action">
        <a href="/post/<?= $this->e($leadStory['slug']) ?>" class="read-more-link">Continue Reading Full Story &rarr;</a>
      </div>
    </article>

    <!-- Grid of Secondary Articles -->
    <?php if (!empty($remainingStories)): ?>
      <div class="editorial-divider">
        <span class="divider-title">Dispatches &amp; Articles</span>
      </div>

      <div class="stories-grid">
        <?php foreach ($remainingStories as $post): ?>
          <article class="grid-story">
            <div class="story-byline">
              <span class="byline-date"><?= date('M j, Y', (int) $post['created_at']) ?></span>
              <?php if ($this->is_admin()): ?>
                <span class="byline-dot">&bull;</span>
                <a href="/admin/edit/<?= (int) ($post['id'] ?? 0) ?>" class="edit-link">Edit</a>
              <?php endif; ?>
            </div>
            <h3 class="grid-headline">
              <a href="/post/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a>
            </h3>
            <p class="grid-excerpt">
              <?php
              $rendered = $this->filter('post_content', $post['content'], $post);
              $plain = trim(strip_tags($rendered));
              echo htmlspecialchars(mb_substr($plain, 0, 150)) . (mb_strlen($plain) > 150 ? '…' : '');
              ?>
            </p>
            <a href="/post/<?= $this->e($post['slug']) ?>" class="grid-link">Read Story &rarr;</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination-broadsheet" aria-label="Edition Navigation">
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?= $currentPage - 1 ?>" class="page-link">&larr; Previous Edition</a>
        <?php else: ?>
          <span class="page-disabled">&larr; Previous Edition</span>
        <?php endif; ?>

        <span class="page-edition">Edition Page <?= $currentPage ?> of <?= $totalPages ?></span>

        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>" class="page-link">Next Edition &rarr;</a>
        <?php else: ?>
          <span class="page-disabled">Next Edition &rarr;</span>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
