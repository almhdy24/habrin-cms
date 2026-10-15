# Upgrade Guide

## From v0.9.x → v1.0.0

🎉 This is a **major stable release** bringing core freeze compliance, comprehensive security hardening, and publishing enhancements.

### What You Must Do
1. Backup your `content/` and `assets/uploads/` folders before updating.
2. Update dependencies using `composer install --no-dev`.
3. Verify that directory `.htaccess` protection files are present in `content/`, `logs/`, `backups/`, and `assets/uploads/`.
4. Ensure your active theme contains `theme.json` with `"cms": ">=0.9.0"`.
5. Run the test suite: `php vendor/bin/phpunit`.

### What Changed
- **Core Stability**: `PostsModel::all()` is fully implemented and adheres to `CORE_FREEZE.md`.
- **Security Hardening**:
  - Removed all default credentials.
  - Implemented CSRF defense-in-depth across administrative actions.
  - Markdown input strips dangerous raw HTML and scripts.
  - Media uploads validate MIME types and sanitize SVGs.
  - Directory traversal prevented in plugin manifests and theme previews.
- **New Capabilities**:
  - Media Library (`/admin/media`)
  - Backup & Restoration with safety snapshots (`/admin/backup`)
  - Drafts workflow with preview banners
  - Search across posts, slugs, and body

### What Is Preserved
- All posts, pages, menu structures, and site settings are 100% backward-compatible.

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

### Recommendation
Upgrade to v0.3.0 for improved security.

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

### What Is Preserved
- Posts (`posts.json`)
- Settings (`settings.json`)
- Content data

### Recommendation
If you are mid-project:
- Stay on v0.1.0
- Upgrade when ready to redesign theme

---

Need help?  
Open an issue on GitHub.
