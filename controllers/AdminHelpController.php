<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;

/**
 * Admin Help Center Controller
 * Provides searchable, in-product guides and documentation for Habrin CMS.
 */
class AdminHelpController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
    }

    public function index(): void
    {
        $guides = $this->getGuides();
        $query = trim((string) ($this->input('q') ?? ''));

        if ($query !== '') {
            $filtered = [];
            $lowerQ = mb_strtolower($query);
            foreach ($guides as $slug => $guide) {
                $haystack = mb_strtolower($guide['title'] . ' ' . $guide['summary'] . ' ' . strip_tags($guide['content']));
                if (str_contains($haystack, $lowerQ)) {
                    $filtered[$slug] = $guide;
                }
            }
            $guides = $filtered;
        }

        $this->view('admin/help/index', [
            'guides'     => $guides,
            'query'      => $query,
            'flash'      => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    /**
     * Get structured help guides with localized text
     * @return array<string, array{id: string, icon: string, title: string, category: string, summary: string, content: string}>
     */
    public function getGuides(): array
    {
        return [
            'getting-started' => [
                'id'       => 'getting-started',
                'icon'     => 'rocket',
                'title'    => __('help.getting_started'),
                'category' => __('nav.maintenance'),
                'summary'  => 'System overview, folder hierarchy, configuration settings, and verification.',
                'content'  => <<<HTML
<p><strong>Habrin CMS</strong> is a modern, privacy-respecting flat-file content management system powered by PHP 8.1+. It does not require MySQL, PostgreSQL, or SQLite databases, storing all records in clean, atomic JSON structures and Markdown text files.</p>
<h4>Key Directories:</h4>
<ul>
  <li><code>content/</code>: Contains sharded articles, static pages, navigation menus, and core settings.</li>
  <li><code>assets/uploads/</code>: Media files, images, and visual assets uploaded via the Media Library.</li>
  <li><code>logs/</code>: System diagnostic logs (<code>app.log</code>) and security audit trails (<code>audit.log</code>).</li>
  <li><code>backups/</code>: Point-in-time ZIP archives generated for data preservation and disaster recovery.</li>
  <li><code>themes/</code>: User interface presentation templates (Plates engine) and stylesheets.</li>
  <li><code>plugins/</code>: Drop-in modular extensions (SEO, Markdown, Content Tools, Analytics, Sitemap).</li>
</ul>
<h4>Initial Configuration:</h4>
<p>Head to <strong>Settings</strong> in the navigation sidebar to customize your site title, SEO tagline, hero banner, and default interface language.</p>
HTML
            ],
            'publishing' => [
                'id'       => 'publishing',
                'icon'     => 'file-text',
                'title'    => __('help.publishing'),
                'category' => __('nav.content'),
                'summary'  => 'Create, preview, schedule, tag, and publish articles and standalone static pages.',
                'content'  => <<<HTML
<h4>Writing & Publishing Articles:</h4>
<ol>
  <li>Navigate to <strong>Articles &rarr; Write New Article</strong>.</li>
  <li>Type your headline in the <strong>Article Title</strong> field.</li>
  <li>Compose your body content in the <strong>Markdown Editor</strong>. You can use full CommonMark syntax or click formatting buttons on the toolbar.</li>
  <li>In the <strong>Article Metadata</strong> panel:
    <ul>
      <li><strong>URL Slug:</strong> Automatically generated from your title. You can customize it anytime; existing slugs are preserved during edits to prevent broken links.</li>
      <li><strong>Tags:</strong> Enter comma-separated keywords (e.g. <code>tech, news, php</code>).</li>
      <li><strong>Summary / Excerpt:</strong> Provide a 1-2 sentence overview. This is used for homepage article cards and search engine meta descriptions.</li>
    </ul>
  </li>
  <li>Set the publishing status to <strong>🟢 Published</strong> for immediate public visibility, or <strong>🟡 Draft</strong> for private preview by logged-in administrators.</li>
  <li>Click <strong>Publish Article</strong>.</li>
</ol>
<h4>Static Pages:</h4>
<p>For standalone pages like <em>About Us</em>, <em>Contact</em>, or <em>Terms</em>, use <strong>Static Pages &rarr; Create New Page</strong>. They are served directly at root URLs like <code>/about</code>.</p>
HTML
            ],
            'markdown-media' => [
                'id'       => 'markdown-media',
                'icon'     => 'image',
                'title'    => __('help.markdown'),
                'category' => __('nav.content'),
                'summary'  => 'Markdown formatting cheatsheet, media uploads, and embedding visual assets.',
                'content'  => <<<HTML
<h4>Markdown Syntax Cheatsheet:</h4>
<pre><code># Main Heading 1
## Subheading 2
### Section Heading 3

**Bold text** or *italicized text*
> Blockquote text for citations

- Unordered bullet point
1. Numbered ordered list

[Link text](https://example.com)
![Image Alt Description](/assets/uploads/photo.jpg)

`inline_code()`

```php
// Fenced code block
echo "Hello from Habrin!";
```

| Header 1 | Header 2 |
| -------- | -------- |
| Value 1  | Value 2  |</code></pre>
<h4>Media Management:</h4>
<p>Upload JPG, PNG, GIF, WEBP, or sanitized SVG files up to 5 MB in <strong>Media Library</strong>. While writing articles, click <strong>Insert Media</strong> in the editor toolbar to select and insert any image with a single click.</p>
HTML
            ],
            'themes-guide' => [
                'id'       => 'themes-guide',
                'icon'     => 'palette',
                'title'    => __('help.themes_guide'),
                'category' => __('nav.structure'),
                'summary'  => 'Switching, previewing, and customizing Habrin themes (default, cleanblog, dark, magazine).',
                'content'  => <<<HTML
<p>Habrin CMS includes four distinct, responsive themes:</p>
<ul>
  <li><strong>Habrin Default:</strong> Elegant signature editorial style with serene green accents, balanced typography, and responsive cards.</li>
  <li><strong>Clean Blog:</strong> Serene reading sanctuary with centered serif typography, generous white space, and reading-time estimations.</li>
  <li><strong>Dark Terminal:</strong> Obsidian slate publication inspired by developer terminals, monospace accents, and high-contrast night aesthetics.</li>
  <li><strong>The Habrin Chronicle (Magazine):</strong> High-density broadsheet newspaper design with a featured lead headline, editorial dispatches, and category ribbons.</li>
</ul>
<p>To switch themes, navigate to <strong>Themes</strong> and click <strong>Activate Theme</strong>. All themes support complete RTL layouts and multilingual tags automatically.</p>
HTML
            ],
            'plugins-guide' => [
                'id'       => 'plugins-guide',
                'icon'     => 'puzzle',
                'title'    => __('help.plugins_guide'),
                'category' => __('nav.structure'),
                'summary'  => 'Modular plugin architecture, lifecycle hooks, and the Content Tools extension.',
                'content'  => <<<HTML
<p>Plugins in Habrin CMS are self-contained modular extensions inside the <code>plugins/</code> folder. Each plugin contains a <code>plugin.json</code> manifest and a main PHP entry script.</p>
<h4>Included Production Plugins:</h4>
<ul>
  <li><strong>Content Tools:</strong> Live word count, estimated reading time, pre-publication validation checklist, missing excerpt detection, and image alt text auditing.</li>
  <li><strong>SEO Manager:</strong> Configurable Open Graph tags, canonical URLs, and schema meta descriptions.</li>
  <li><strong>Markdown Renderer:</strong> Secure CommonMark parser with front-matter extraction and XSS output sanitization.</li>
  <li><strong>XML Sitemap:</strong> Automatic dynamic <code>/sitemap.xml</code> generation for search engine webmasters.</li>
  <li><strong>Analytics:</strong> Privacy-conscious Google Analytics / Tag Manager embedder.</li>
</ul>
<p>You can enable, disable, and configure extensions under <strong>Plugins</strong> without modifying core code.</p>
HTML
            ],
            'backup-recovery' => [
                'id'       => 'backup-recovery',
                'icon'     => 'archive',
                'title'    => __('help.backup_recovery'),
                'category' => __('nav.maintenance'),
                'summary'  => 'Point-in-time content archives, automated recovery, and disaster prevention.',
                'content'  => <<<HTML
<p>Protecting your publication's content is effortless with Habrin's built-in backup engine:</p>
<h4>Creating a Backup:</h4>
<ol>
  <li>Go to <strong>Backups</strong> in the sidebar.</li>
  <li>Click <strong>Create New Backup</strong>.</li>
  <li>Habrin bundles your <code>content/</code> JSON databases, static pages, menus, settings, and media records into an atomic ZIP archive stored in <code>backups/</code>.</li>
  <li>Click <strong>Download ZIP</strong> to store an offline copy on your local computer or secure cold storage.</li>
</ol>
<h4>Restoring from Backup:</h4>
<p>Click <strong>Restore</strong> next to any archive. The CMS validates the archive integrity before replacing your content, ensuring you can revert accidental deletions instantly.</p>
HTML
            ],
            'security-guide' => [
                'id'       => 'security-guide',
                'icon'     => 'shield',
                'title'    => __('help.security_guide'),
                'category' => __('nav.maintenance'),
                'summary'  => 'Credential management, CSRF protection, brute-force mitigation, and server hardening.',
                'content'  => <<<HTML
<h4>Security Best Practices:</h4>
<ul>
  <li><strong>Strong Administrator Credentials:</strong> Update your credentials under <strong>Settings &rarr; Administrator Account & Security</strong>. Always use a passphrase with at least 8 characters.</li>
  <li><strong>Brute Force Throttling:</strong> Failed admin logins trigger progressive exponential backoff and lockouts after 5 consecutive failures.</li>
  <li><strong>CSRF Defense:</strong> Every administrative POST action requires a cryptographic 256-bit token.</li>
  <li><strong>Path Traversal & Execution Defense:</strong> All file uploads are stored in <code>assets/uploads/</code> with strict MIME verification and execution denial rules in <code>.htaccess</code>. PHP scripts cannot execute inside upload folders.</li>
  <li><strong>Folder Permissions:</strong> Ensure <code>content/</code>, <code>logs/</code>, and <code>backups/</code> have 0755 permissions and are protected from direct web browsing by the included access-denial rules.</li>
</ul>
HTML
            ],
            'multilingual-guide' => [
                'id'       => 'multilingual-guide',
                'icon'     => 'globe',
                'title'    => __('help.multilingual_guide'),
                'category' => __('nav.maintenance'),
                'summary'  => 'Interface localization, English, Arabic (RTL), French, Spanish, and layout direction.',
                'content'  => <<<HTML
<p>Habrin CMS features native, zero-dependency interface localization across four major world languages:</p>
<ul>
  <li>🇬🇧 <strong>English (en)</strong>: Left-to-Right (LTR)</li>
  <li>🇸🇦 <strong>العربية (ar)</strong>: Right-to-Left (RTL) with mirrored sidebars, native Arabic typography, and reverse layouts.</li>
  <li>🇫🇷 <strong>Français (fr)</strong>: Left-to-Right (LTR)</li>
  <li>🇪🇸 <strong>Español (es)</strong>: Left-to-Right (LTR)</li>
</ul>
<h4>Switching Languages:</h4>
<p>Use the language selector in the top-right header of the admin panel. Your selection persists across sessions and is saved in a secure 1-year cookie. You can also configure the site-wide default language in <strong>Settings</strong>.</p>
<h4>Centralized Translation Files:</h4>
<p>All interface strings are organized in <code>languages/{code}.php</code>. To add custom phrases or adjust wording, edit the corresponding dictionary file without altering template logic.</p>
HTML
            ],
        ];
    }
}
