# Pico JSON CMS

A lightweight, secure, and fast flat-file CMS built with PHP 8.1+.

Store content in structured JSON, write in Markdown, upload media securely, and publish instantly with a responsive admin panel — without database configuration, Node.js, or complex infrastructure.

[![Release](https://img.shields.io/github/v/release/almhdy24/pico-json-cms?label=release)](https://github.com/almhdy24/pico-json-cms/releases)
[![PHP](https://img.shields.io/badge/php-8.1%2B-blue)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-37%20passed%20%7C%20112%20assertions-success)](tests/)

---

## 🎯 Highlights

- **Zero Database Required**: Pure flat-file persistence with atomic writes and sharded JSON storage.
- **Security-First Architecture**: Strictly verified CSRF tokens, session fixation defense, login lockout backoff, SVG sanitization, MIME verification, and path traversal prevention.
- **Web-Based Installer**: Guided initial setup with strong password enforcement and automatic installation locking.
- **Media Library**: Drag-and-drop file uploads with extension whitelist, MIME inspection, XSS-safe SVG handling, and instant Markdown copy snippets.
- **Backup & Safe Restore**: Native ZIP / JSON exports with automatic pre-restore safety snapshots and zip path traversal validation.
- **Drafts & Publishing Workflow**: Seamless draft vs. published state management with private admin preview banners.
- **Full-Text Search**: Fast, incremental search across post titles, content, and slugs.
- **Extensible Hook System**: WordPress-style actions and filters for plugins and customizable themes.
- **Themes & Layouts**: Server-rendered Plates templates with fallback inheritance.

---

## 🚀 Quick Start

### 1. Requirements

- PHP 8.1.0 or higher
- PHP extensions: `json`, `mbstring`, `fileinfo`, `zip` (optional for zip backups)
- Apache / Nginx with URL rewriting enabled (`mod_rewrite` for Apache)

### 2. Installation

Clone the repository and install Composer dependencies:

```bash
git clone https://github.com/almhdy24/pico-json-cms.git
cd pico-json-cms
composer install --no-dev --optimize-autoloader
```

Ensure web-server write permissions on storage directories:

```bash
chmod -R 775 content logs backups assets/uploads
```

### 3. Run the Web Installer

Start the built-in development server (or point your virtual host to the directory):

```bash
php -S localhost:8000
```

Navigate to:

```text
http://localhost:8000/installer
```

The installer verifies environment compatibility, initializes core configuration, and prompts you to configure your primary administrator account with a strong password. Once finished, it creates `content/.installed` to permanently lock installer endpoints.

---

## 🔒 Security Hardening

Pico JSON CMS incorporates defense-in-depth protections:

1. **No Default Credentials**:
   - The installer mandates a custom administrator password and rejects predictable defaults.
   - Passwords are encrypted with `PASSWORD_BCRYPT`.
   - Admin credentials are stored outside the web root or strictly protected in `content/.admin.php` with direct execution prevention (`defined('BASE_PATH') || exit;`).

2. **Session Security & Login Throttling**:
   - Cookie parameters: `HttpOnly`, `SameSite=Lax`, and dynamic `Secure` over HTTPS.
   - Session identifiers are regenerated upon authentication.
   - Login attempts trigger progressive backoff and 5-minute lockouts after 5 consecutive failures.
   - Inactive sessions expire after 30 minutes.

3. **CSRF Protection**:
   - State-changing actions (publish, edit, delete, trash, restore, destroy, upload, restore backup) strictly validate cryptographically secure CSRF tokens on POST requests.

4. **Input Sanitization & Output Escaping**:
   - Markdown rendering strips raw HTML tags (`html_input => strip`) and disallows unsafe URI schemes.
   - Fallback rendering escapes HTML entities with UTF-8 support.
   - Strict regex validation on analytics tracking IDs and theme preview parameters.

5. **Upload Validation**:
   - Whitelist of permitted extensions (`jpg`, `jpeg`, `png`, `gif`, `webp`, `svg`).
   - File size enforced (5 MB maximum).
   - Strict MIME inspection via `finfo`.
   - SVG files are checked for embedded scripts, iframes, and `on*` event handlers.
   - Script execution is blocked via `assets/uploads/.htaccess`.

6. **Directory Access Restrictions**:
   - Distributed `.htaccess` files enforce `Require all denied` on `content/`, `logs/`, and `backups/`.

---

## 🛠️ Testing & Verification

Run the automated test suite:

```bash
php vendor/bin/phpunit
```

Test coverage includes:
- **RouterTest**: Route registration, matching, dynamic parameter extraction (`/post/{slug}`).
- **AuthTest**: State checks, password verification, CSRF lifecycle, session timeouts.
- **PostsModelTest**: CRUD, sharded storage, slug collision resolution, drafts isolation, search, trash, and base contract compliance.
- **MediaModelTest**: Image uploads, MIME validation, extension whitelisting, SVG XSS sanitization, deletion safeguards.
- **BackupModelTest**: ZIP/JSON archive generation, path traversal rejection during restoration, deletion safety.
- **SecurityTest**: Path traversal in plugin manifests and theme previews, HTML stripping in Markdown, directory access rules.
- **PagesAndMenuModelTest**: Static page persistence and hierarchical menu order.

---

## 📁 Project Layout

```text
pico-json-cms/
├── assets/             # Static CSS, JS, and uploads
│   └── uploads/        # User media uploads (protected against script execution)
├── backups/            # System backup archives (access denied)
├── content/            # JSON data storage, shards, and indexes (access denied)
│   ├── indexes/        # Incremental slug and pagination indexes
│   └── posts/          # Sharded post JSON files
├── controllers/        # Application MVC controllers
├── core/               # Frozen core architecture (App, Router, Auth, Session, Hooks)
├── docs/               # Technical guides, deployment, and API references
├── logs/               # Application runtime and audit logs (access denied)
├── models/             # Business logic and JSON persistence models
├── plugins/            # Plugin modules (Markdown, SEO, Sitemap, Analytics)
├── tests/              # PHPUnit regression and integration test suite
├── themes/             # Frontend themes and admin layouts
├── config.php          # Main site configuration
├── index.php           # Public web entry point
└── composer.json       # Project dependencies and autoloading definitions
```

---

## 📖 Documentation

- [Deployment Guide](docs/DEPLOY.md)
- [Complete User Guide](docs/guide.md)
- [Admin Manual](docs/admin.md)
- [Frequently Asked Questions](docs/faq.md)
- [Upgrade Guide](UPGRADE.md)
- [Core Freeze Contract](CORE_FREEZE.md)

---

## 📄 License

This software is open-source under the [MIT License](LICENSE).
Created and maintained by [Elmahdi Abdallh](https://github.com/almhdy24).
