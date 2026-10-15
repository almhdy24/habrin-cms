# Upgrade Guide

## From Pico JSON CMS (v0.9.x) → Habrin CMS (v1.0.0)

🎉 **Habrin CMS v1.0.0** is a major stable release that establishes a distinct product identity, a responsive mobile-first administrative design system, and full `CORE_FREEZE.md` API stability.

### What You Must Do
1. **Backup Data**: Export a backup via `/admin/backup` or make a copy of your `content/` and `assets/uploads/` folders before updating.
2. **Update Dependencies**: Run `composer install --no-dev --optimize-autoloader`. The root composer package is now `habrin/habrin`.
3. **Verify Security Rules**: Ensure `.htaccess` files are present in `content/`, `logs/`, `backups/`, and `assets/uploads/`.
4. **Theme Compatibility**: Themes remain 100% compatible. Ensure `theme.json` contains `"cms": ">=0.9.0"`.
5. **Run Verification**: Run `php vendor/bin/phpunit` to confirm all 37 regression tests pass.

### What Changed
- **Product Identity & Branding**:
  - Rebranded to **Habrin CMS**.
  - Default session cookie renamed to `habrin_session`.
  - SVG brand mark assets added to `assets/images/habrin-logo.svg`.
- **Mobile-First Design System**:
  - Full-screen collapsible drawer navigation with hamburger toggle for phones and tablets.
  - Sticky mobile header for one-tap navigation.
  - Editorial 2-column post editor with real-time word/character counter and responsive collapse.
  - Non-blocking toast notification engine (`window.habrinToast`) replacing invasive browser alerts.
  - Touch target compliance ($\ge$ 44px) across all buttons, inputs, and links.
- **Core Freeze Compliance**:
  - `PostsModel::all()` implemented adhering to frozen `Core\Model` contracts.
  - All frozen APIs (`Core\App`, `Core\Router`, `Core\Auth`, `Core\Session`, `Core\Hooks`, `Core\Model`, `Core\View`) fully preserved.
- **Security Hardening**:
  - Predictable default credentials removed; initial administrator setup enforced via `/installer`.
  - CSRF defense-in-depth across all administrative actions.
  - XSS prevention with HTML stripping and safe URI protocols in Markdown.
  - Media upload MIME verification, extension whitelisting, and SVG sanitization.
  - Path traversal protection in plugin manifests, theme previews, and ZIP backup extraction.
- **New Publishing Workflows**:
  - Media Library (`/admin/media`) with one-click Markdown snippets.
  - One-click backups with automatic pre-restore safety snapshots (`/admin/backup`).
  - Drafts workflow with private administrator preview banners.
  - Fast search across titles, slugs, and body content.

### What Is Preserved
- 100% backward-compatibility for existing `content/posts/`, `content/pages.json`, `content/menu.json`, and `content/settings.json`.
- No database migration or schema conversions required.

---

## From v0.2.0 → v0.3.0

⚠️ This is a **minor security** upgrade.

### What You Must Do
1. Backup `content/`
2. Replace all project files
3. Run the installer at `/installer` (if starting fresh)
4. If upgrading, ensure `.htaccess` is present
5. Clear browser cache

### What Changed
- Added CSRF protection
- Removed `admin.php` from root
- Added `.htaccess`
- Improved input sanitization

### What Is Preserved
- Posts (`posts.json`)
- Settings (`settings.json`)
- Content data

---

## From v0.1.0 → v0.2.0

⚠️ This is a **breaking upgrade**.

### What You Must Do
1. Backup `content/`
2. Replace all project files
3. Run the installer at `/installer`
4. Re-apply settings via admin panel
5. Update or recreate your theme

### What Changed
- New installer system
- New theme structure
- Plugin system refactor
- Markdown engine changed
- Admin entry removed

---

Need help?  
Open an issue on GitHub: https://github.com/almhdy24/pico-json-cms/issues
