# Complete User & Administrator Guide

## Overview

Pico JSON CMS is a fast, flat-file content management system designed for simplicity, security, and developer control. It requires no relational database, storing all data in sharded JSON structures and rendering content with lightweight Plates PHP templates.

---

## Installation & First Run

### 1. Requirements
- PHP 8.1.0 or higher
- Composer
- Required extensions: `json`, `mbstring`, `fileinfo`
- Recommended: `zip` (for native ZIP backup creation and restoration)

### 2. Setup
Clone the repository and install Composer packages:

```bash
git clone https://github.com/almhdy24/pico-json-cms.git
cd pico-json-cms
composer install --no-dev
```

Set file permissions so the web server can write to data directories:

```bash
chmod -R 775 content logs backups assets/uploads
```

### 3. Web Installer
Launch the built-in development server:

```bash
php -S localhost:8000
```

Visit `http://localhost:8000/installer` in your browser.

The installer will:
1. Verify system requirements and write permissions.
2. Initialize default site settings (`content/settings.json`, `content/pages.json`, `content/menu.json`).
3. Securely create your first administrator account with a strong password of your choice.
4. Create the lock file (`content/.installed`) to permanently disable the installer.

---

## Content Publishing Workflow

### Creating and Editing Posts
1. Log in to `/admin/login`.
2. Click **New Post** in the sidebar.
3. Provide a **Title**. A URL-friendly slug is generated automatically.
4. Choose the status:
   - **Published**: The post will immediately appear on the homepage and feed.
   - **Draft**: The post is saved privately. Unauthenticated visitors cannot view it. Logged-in administrators can preview drafts at `/post/your-slug` with a visible preview banner.
5. Write your content using Markdown.
6. Click **Save Post**.

### Media Library
1. Navigate to `/admin/media`.
2. Drag and drop or browse for images (`JPG`, `PNG`, `GIF`, `WEBP`, `SVG`).
3. Once uploaded, click the **Copy Markdown** snippet button.
4. Paste the snippet directly into your Markdown editor.
5. Uploaded files are protected against arbitrary code execution by server configuration.

### Search Functionality
- Both the public site and admin dashboard support full-text search across titles, slugs, and body content.
- Administrators can search both published posts and drafts. Public visitors only search published posts.

---

## Backup & Recovery

1. Navigate to `/admin/backup`.
2. Click **Create Backup Now**. The CMS packages all posts, pages, menu configurations, and settings into a timestamped `.zip` archive (with `.json` fallback).
3. Download the archive for offsite storage.
4. To restore, upload a previously exported backup archive. The CMS automatically generates a pre-restoration safety snapshot before restoring any files, and verifies archive entries against directory traversal attacks (`../`).

---

## Theme & Plugin Management

- **Themes**: Switch between themes under `/admin/themes`. Active themes inherit layouts and templates from the `default` theme fallback.
- **Plugins**: Activate, deactivate, and configure plugins (such as SEO metadata, Sitemap generation, and Google Analytics) under `/admin/plugins`.

---

## Security Architecture

- **Authentication**: Salted `password_hash` (bcrypt) with progressive delay on repeated login failures and account lockout.
- **CSRF Tokens**: All administrative POST requests are protected by cryptographically random CSRF tokens.
- **XSS Protection**: Markdown input strips dangerous HTML tags and event attributes.
- **Directory Protection**: Sensitive directories (`content/`, `logs/`, `backups/`) deny all direct HTTP access via `.htaccess`.