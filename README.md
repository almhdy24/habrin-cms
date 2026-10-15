# Habrin CMS

A modern, lightweight, fast, and mobile-friendly flat-file CMS built with PHP 8.1+.

Store content in structured JSON, write in Markdown, manage media effortlessly, and publish instantly with a polished, touch-first admin panel — without databases, Node.js, or complex infrastructure.

[![Release](https://img.shields.io/github/v/release/almhdy24/pico-json-cms?label=release)](https://github.com/almhdy24/pico-json-cms/releases)
[![PHP](https://img.shields.io/badge/php-8.1%2B-blue)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-37%20passed%20%7C%20112%20assertions-success)](tests/)

---

## 🎨 Highlights & Design System

- **Zero Database Required**: Pure flat-file persistence with atomic writes, sharded JSON storage, and slug indexing.
- **Mobile-First Responsive Admin**:
  - Full-screen collapsible drawer sidebar with hamburger toggle on phones and tablets.
  - Sticky mobile header for quick dashboard access and status awareness.
  - High-contrast, touch-friendly UI components (all interactive controls $\ge$ 44px).
  - Modern typography, layered SVG brand mark, and dark slate dashboard styling.
- **Editorial Experience**:
  - 2-column post editor (editor canvas on left, publishing metadata & status on right).
  - Live word and character counter.
  - Drafts vs. Published states with dedicated status badges and authenticated draft preview banners.
  - Fast full-text search across titles, slugs, and body.
- **Interactive Media Library**:
  - Drag-and-drop file uploads with instant non-blocking toast notifications (`window.habrinToast`).
  - One-click Markdown embed snippet copy.
  - Strict MIME type verification, extension whitelist, and automated SVG XSS sanitization.
- **Safe Backups & Snapshots**:
  - One-click ZIP / JSON archives with path traversal verification.
  - Automated pre-restore safety snapshots before any archive is applied.
- **Security-First Architecture**:
  - Cryptographically secure CSRF protection on all state-changing actions.
  - Session fixation protection, secure cookie flags (`HttpOnly`, `SameSite=Lax`, HTTPS `Secure`).
  - Progressive login backoff throttling with 5-minute lockout after 5 failed attempts.
  - Direct execution prevention with distributed `.htaccess` protections.
- **Plates Template Engine**:
  - Server-rendered templates with clean inheritance, fallback resolution, and customizable themes (`default`, `cleanblog`, `dark`, `magazine`).
- **Unified Hooks Architecture**:
  - WordPress-style action and filter hooks (`Hooks::add_action`, `Hooks::add_filter`) with plugin error isolation.

---

## 🚀 Quick Start

### 1. Requirements

- PHP 8.1.0 or higher
- PHP extensions: `json`, `mbstring`, `fileinfo`, `zip` (optional, for ZIP backups)
- Web server: Apache (with `mod_rewrite`), Nginx, Caddy, or PHP built-in server

### 2. Installation

Clone the repository and install Composer dependencies:

```bash
git clone https://github.com/almhdy24/pico-json-cms.git habrin-cms
cd habrin-cms
composer install --no-dev --optimize-autoloader
```

Set server write permissions on the data and runtime directories:

```bash
chmod -R 775 content logs backups assets/uploads
```

### 3. Run the Web Installer

Start the built-in development server (or configure your virtual host to serve the directory):

```bash
php -S localhost:8000
```

Navigate to:

```text
http://localhost:8000/installer
```

The 3-step installer will:
1. Verify system requirements, PHP version, and directory permissions.
2. Initialize site settings and menu configuration.
3. Prompt you to securely create your primary administrator account with a strong password.
4. Permanently seal installer access by generating `content/.installed`.

---

## 🔒 Security Hardening

Habrin CMS is built with defense-in-depth principles:

1. **No Insecure Default Credentials**:
   - The installer mandates a custom administrator password and rejects predictable defaults.
   - Passwords are encrypted with `PASSWORD_BCRYPT`.
   - Admin credentials are stored protected in `content/.admin.php` with direct execution prevention (`defined('BASE_PATH') || exit;`).

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

## 🛠️ Testing & Quality Assurance

Run the automated test suite with PHPUnit:

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

## 📁 Directory Structure

```text
habrin-cms/
├── assets/             # Static CSS, JS, logo SVG, and uploads
│   ├── images/         # Official Habrin brand marks and SVGs
│   └── uploads/        # Uploaded media (execution disabled via .htaccess)
├── backups/            # System backup archives (access denied)
├── content/            # JSON data storage, shards, and indexes (access denied)
│   ├── indexes/        # Slug and pagination indexes
│   └── posts/          # Sharded post JSON files
├── controllers/        # Application MVC controllers
├── core/               # Frozen core architecture (App, Router, Auth, Session, Hooks)
├── docs/               # Technical guides, deployment, and API references
├── logs/               # Application runtime and audit logs (access denied)
├── models/             # Business logic and JSON persistence models
├── plugins/            # Extensibility modules (Markdown, SEO, Sitemap, Analytics)
├── tests/              # PHPUnit regression and integration test suite
├── themes/             # Frontend themes and admin layouts
│   ├── cleanblog/      # Minimalist typography theme
│   ├── dark/           # High-contrast dark theme
│   ├── default/        # Habrin flagship theme & admin design system
│   └── magazine/       # Multi-column editorial theme
├── config.php          # Main site configuration
├── index.php           # Public web entry point
└── composer.json       # Project dependencies and autoloading definitions
```

---

## 📖 Documentation

- [Complete User Guide](docs/guide.md)
- [Admin Manual & Mobile Usage](docs/admin.md)
- [Deployment Guide](docs/DEPLOY.md)
- [Frequently Asked Questions](docs/faq.md)
- [Upgrade Guide](UPGRADE.md)
- [Core Freeze Contract](CORE_FREEZE.md)

---

## 📄 License

This software is open-source under the [MIT License](LICENSE).  
Created and maintained by [Elmahdi Abdallh](https://github.com/almhdy24) and the Habrin CMS contributors.
