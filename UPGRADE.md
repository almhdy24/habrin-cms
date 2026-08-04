# Upgrade Guide

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
