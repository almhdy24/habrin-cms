<?php

declare(strict_types=1);

/**
 * Habrin CMS - Content Tools Plugin
 * Production-ready writing and publishing intelligence:
 * - Real-time word count & reading time estimation
 * - Missing metadata warnings (excerpts, tags, titles)
 * - Missing image alt-text auditing
 * - Pre-publication preflight checklist integrated directly into the editor
 */

use Core\Hooks;
use Core\PluginSettings;
use Core\View;

// Register configurable settings schema
PluginSettings::register('content_tools', [
    'title'  => 'Content Tools Settings',
    'fields' => [
        'reading_speed' => [
            'type'        => 'number',
            'label'       => 'Reading Speed (Words Per Minute)',
            'default'     => 200,
            'description' => 'Average reading speed used to calculate estimated reading time (standard: 200 wpm).',
            'sanitize'    => 'int',
        ],
        'min_words' => [
            'type'        => 'number',
            'label'       => 'Minimum Recommended Words',
            'default'     => 150,
            'description' => 'Warn authors if article word count is below this threshold.',
            'sanitize'    => 'int',
        ],
        'require_excerpt' => [
            'type'        => 'checkbox',
            'label'       => 'Warn if Summary/Excerpt is Missing',
            'default'     => true,
            'description' => 'Display pre-publication advisory when an article lacks an SEO summary/excerpt.',
            'sanitize'    => 'bool',
        ],
        'require_tags' => [
            'type'        => 'checkbox',
            'label'       => 'Warn if Tags are Missing',
            'default'     => false,
            'description' => 'Encourage taxonomy categorization by checking for missing tags.',
            'sanitize'    => 'bool',
        ],
        'check_images_alt' => [
            'type'        => 'checkbox',
            'label'       => 'Audit Image Alt Text for Accessibility',
            'default'     => true,
            'description' => 'Flag Markdown images that do not contain descriptive alt text.',
            'sanitize'    => 'bool',
        ],
        'show_public_badge' => [
            'type'        => 'checkbox',
            'label'       => 'Show Reading Time Badge on Public Articles',
            'default'     => true,
            'description' => 'Display estimated reading time badge above post content.',
            'sanitize'    => 'bool',
        ],
    ],
]);

/**
 * Calculate reading time in whole minutes
 */
function content_tools_reading_time(string $content, int $wpm = 200): int
{
    $wordCount = content_tools_word_count($content);
    return max(1, (int) ceil($wordCount / max(50, $wpm)));
}

/**
 * Calculate accurate word count from markdown or HTML content
 */
function content_tools_word_count(string $content): int
{
    $plain = strip_tags($content);
    // Strip markdown formatting characters
    $clean = preg_replace('/[#*_`~\[\]\(\)\>\!\-\+\|\=]/', ' ', $plain) ?? $plain;
    return str_word_count(trim($clean));
}

/**
 * Comprehensive pre-publication analyzer
 * @return array{word_count: int, reading_time: int, passes: bool, checks: array<string, array{status: string, message: string}>}
 */
function content_tools_analyze(string $title, string $content, string $excerpt = '', array $tags = []): array
{
    $settings = PluginSettings::getEffective('content_tools');
    $wpm = (int) ($settings['reading_speed'] ?? 200);
    $minWords = (int) ($settings['min_words'] ?? 150);
    $reqExcerpt = (bool) ($settings['require_excerpt'] ?? true);
    $reqTags = (bool) ($settings['require_tags'] ?? false);
    $checkAlt = (bool) ($settings['check_images_alt'] ?? true);

    $wordCount = content_tools_word_count($content);
    $readingTime = content_tools_reading_time($content, $wpm);

    $checks = [];
    $allOk = true;

    // 1. Title check
    if (mb_strlen(trim($title)) < 5) {
        $checks['title'] = ['status' => 'warn', 'message' => 'Headline is very short or missing.'];
        $allOk = false;
    } else {
        $checks['title'] = ['status' => 'ok', 'message' => 'Headline length is optimal.'];
    }

    // 2. Word count check
    if ($wordCount < $minWords) {
        $checks['length'] = [
            'status'  => 'warn',
            'message' => "Article length is {$wordCount} words (recommended minimum: {$minWords}).",
        ];
    } else {
        $checks['length'] = [
            'status'  => 'ok',
            'message' => "Substantial content depth ({$wordCount} words, ~{$readingTime} min read).",
        ];
    }

    // 3. Excerpt check
    if ($reqExcerpt && mb_strlen(trim($excerpt)) === 0) {
        $checks['excerpt'] = [
            'status'  => 'warn',
            'message' => 'No summary/excerpt specified. Highly recommended for search engines & social cards.',
        ];
    } elseif (mb_strlen(trim($excerpt)) > 0) {
        $checks['excerpt'] = ['status' => 'ok', 'message' => 'SEO summary/excerpt is configured.'];
    }

    // 4. Tags check
    if ($reqTags && empty($tags)) {
        $checks['tags'] = ['status' => 'warn', 'message' => 'No tags assigned to this article.'];
    } elseif (!empty($tags)) {
        $checks['tags'] = ['status' => 'ok', 'message' => count($tags) . ' taxonomy tag(s) assigned.'];
    }

    // 5. Image Alt text check
    if ($checkAlt && preg_match('/!\[\s*\]\([^\)]+\)/', $content)) {
        $checks['images'] = [
            'status'  => 'warn',
            'message' => 'Found one or more images missing descriptive alt text (e.g. ![]()).',
        ];
    } else {
        $checks['images'] = ['status' => 'ok', 'message' => 'Image accessibility checks passed.'];
    }

    return [
        'word_count'   => $wordCount,
        'reading_time' => $readingTime,
        'passes'       => $allOk,
        'checks'       => $checks,
    ];
}

// Hook into Admin Head: inject interactive Content Tools widget into post editor
Hooks::add_action('admin_head', function () {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (!str_contains($uri, '/admin/add') && !str_contains($uri, '/admin/edit')) {
        return;
    }

    $settings = PluginSettings::getEffective('content_tools');
    $wpm = (int) ($settings['reading_speed'] ?? 200);
    $minWords = (int) ($settings['min_words'] ?? 150);
    $reqExcerpt = !empty($settings['require_excerpt']) ? 'true' : 'false';
    $reqTags = !empty($settings['require_tags']) ? 'true' : 'false';
    $checkAlt = !empty($settings['check_images_alt']) ? 'true' : 'false';

    echo <<<HTML
<style>
.content-tools-card {
  background: var(--paper);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.25rem;
  margin-bottom: 1.25rem;
}
.content-tools-card h4 {
  font-size: 0.95rem;
  margin: 0 0 0.75rem 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.ct-metrics {
  display: flex;
  gap: 1rem;
  padding: 0.5rem 0.75rem;
  background: var(--bg-soft);
  border-radius: 6px;
  font-size: 0.85rem;
  margin-bottom: 0.75rem;
}
.ct-metrics strong { color: var(--primary); }
.ct-checklist {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 0.8rem;
}
.ct-checklist li {
  display: flex;
  align-items: flex-start;
  gap: 0.45rem;
  margin-bottom: 0.35rem;
  line-height: 1.4;
}
.ct-badge-ok { color: var(--primary); font-weight: bold; }
.ct-badge-warn { color: #d97706; font-weight: bold; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const postForm = document.getElementById('postForm');
  if (!postForm) return;

  const sidebar = postForm.querySelector('div[style*="width: 320px"]');
  if (!sidebar) return;

  // Create Inspector Card
  const card = document.createElement('div');
  card.className = 'content-tools-card';
  card.innerHTML = `
    <h4>
      <span><i data-lucide="check-square" style="width:16px;height:16px;vertical-align:-2px;color:var(--primary);"></i> Content Tools Inspector</span>
      <span id="ctReadBadge" class="badge" style="background:var(--bg-soft);font-size:0.75rem;">1 min read</span>
    </h4>
    <div class="ct-metrics">
      <div>Words: <strong id="ctWordsCount">0</strong></div>
      <div>Est. Read: <strong id="ctReadingTime">1m</strong></div>
    </div>
    <ul class="ct-checklist" id="ctChecklist"></ul>
  `;

  // Insert before the Move to Trash card or at bottom of sidebar
  const trashCard = sidebar.querySelector('div[style*="border-color: rgba(239, 68, 68"]');
  if (trashCard) {
    sidebar.insertBefore(card, trashCard);
  } else {
    sidebar.appendChild(card);
  }

  const wpm = {$wpm};
  const minWords = {$minWords};
  const reqExcerpt = {$reqExcerpt};
  const reqTags = {$reqTags};
  const checkAlt = {$checkAlt};

  function auditContent() {
    let content = '';
    const contentEl = document.getElementById('content');
    if (window.EasyMDE && contentEl) {
      const cm = contentEl.nextElementSibling;
      if (cm && cm.CodeMirror) {
        content = cm.CodeMirror.getValue();
      } else {
        content = contentEl.value;
      }
    } else if (contentEl) {
      content = contentEl.value;
    }

    const titleEl = document.getElementById('postTitle');
    const excerptEl = document.getElementById('postExcerpt');
    const tagsEl = document.getElementById('postTags');

    const title = titleEl ? titleEl.value.trim() : '';
    const excerpt = excerptEl ? excerptEl.value.trim() : '';
    const tags = tagsEl ? tagsEl.value.trim() : '';

    const words = content.trim() ? content.trim().split(/\\s+/).filter(Boolean).length : 0;
    const readMin = Math.max(1, Math.ceil(words / wpm));

    const wordsEl = document.getElementById('ctWordsCount');
    const timeEl = document.getElementById('ctReadingTime');
    const badgeEl = document.getElementById('ctReadBadge');
    const checklist = document.getElementById('ctChecklist');

    if (wordsEl) wordsEl.textContent = words;
    if (timeEl) timeEl.textContent = readMin + 'm';
    if (badgeEl) badgeEl.textContent = readMin + ' min read';

    if (!checklist) return;

    const items = [];

    // Headline check
    if (title.length >= 5) {
      items.push('<li class="ct-badge-ok">✓ Headline length optimal</li>');
    } else {
      items.push('<li class="ct-badge-warn">⚠ Headline is too short</li>');
    }

    // Word count check
    if (words >= minWords) {
      items.push('<li class="ct-badge-ok">✓ Content length optimal (' + words + ' words)</li>');
    } else {
      items.push('<li class="ct-badge-warn">⚠ Article is short (' + words + '/' + minWords + ' words)</li>');
    }

    // Excerpt check
    if (excerpt.length > 0) {
      items.push('<li class="ct-badge-ok">✓ SEO excerpt configured</li>');
    } else if (reqExcerpt) {
      items.push('<li class="ct-badge-warn">⚠ Missing summary / excerpt</li>');
    }

    // Tags check
    if (tags.length > 0) {
      items.push('<li class="ct-badge-ok">✓ Taxonomy tags configured</li>');
    } else if (reqTags) {
      items.push('<li class="ct-badge-warn">⚠ No tags specified</li>');
    }

    // Image Alt text check
    if (checkAlt) {
      if (/!\\[\\s*\\]\\([^\\)]+\\)/.test(content)) {
        items.push('<li class="ct-badge-warn">⚠ Image missing alt text description</li>');
      } else if (content.includes('![')) {
        items.push('<li class="ct-badge-ok">✓ All images have descriptive alt text</li>');
      }
    }

    checklist.innerHTML = items.join('');
    if (window.lucide) lucide.createIcons();
  }

  // Bind events
  const titleEl = document.getElementById('postTitle');
  const excerptEl = document.getElementById('postExcerpt');
  const tagsEl = document.getElementById('postTags');

  if (titleEl) titleEl.addEventListener('input', auditContent);
  if (excerptEl) excerptEl.addEventListener('input', auditContent);
  if (tagsEl) tagsEl.addEventListener('input', auditContent);

  setInterval(auditContent, 1500);
  auditContent();
});
</script>
HTML;
});

// Hook into single post view: show reading time badge if enabled
Hooks::add_filter('post_content', function (string $rendered, array $post = []): string {
    $settings = PluginSettings::getEffective('content_tools');
    if (empty($settings['show_public_badge']) || empty($post['content'])) {
        return $rendered;
    }

    // Show badge only on single article views
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (!str_starts_with($uri, '/post/')) {
        return $rendered;
    }

    $wpm = (int) ($settings['reading_speed'] ?? 200);
    $readMin = content_tools_reading_time($post['content'], $wpm);
    $words = content_tools_word_count($post['content']);

    $badge = sprintf(
        '<div class="content-tools-badge" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.82rem;color:var(--text-muted);background:var(--bg-soft);padding:0.25rem 0.65rem;border-radius:4px;margin-bottom:1.25rem;border:1px solid var(--border);"><i data-lucide="clock" style="width:13px;height:13px;"></i> <span>%d min read (%d words)</span></div>',
        $readMin,
        $words
    );

    return $badge . "\n" . $rendered;
}, 5);
