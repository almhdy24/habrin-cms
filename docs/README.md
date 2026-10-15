# Habrin CMS Documentation

**A modern, lightweight, mobile-friendly flat-file CMS that stores everything in JSON files. No database needed.**

---

## What is Habrin CMS?

Habrin CMS is a PHP 8.1+ content management system designed for independent publishing, performance, and developer control:
- Stores all data in `content/` JSON files using atomic writes and sharded indexing.
- Features a responsive, touch-friendly administrative workspace with mobile drawer navigation.
- Native editorial workspace with a 2-column post editor, live word counters, and draft previews.
- Built-in media manager with drag-and-drop file uploads, SVG sanitization, and instant Markdown snippets.
- Extensible through WordPress-style actions and filters.
- Zero database or Redis requirement.

## Quick Start

1. Clone or download the repository.
2. Install Composer packages: `composer install --no-dev`.
3. Set writable permissions on `content/`, `logs/`, `backups/`, and `assets/uploads/`.
4. Start your PHP web server (`php -S localhost:8000`).
5. Complete the 3-step setup at `/installer` to create your initial administrator account.
6. Access the admin panel at `/admin/login`.

## Ideal Use Cases

| Good for... | Not suited for... |
|-------------|-------------------|
| Personal blogs and journals | Multi-vendor e-commerce marketplaces |
| Developer documentation | Highly concurrent enterprise platforms |
| Portfolio and agency websites | Massive social networks |
| Fast static & dynamic project sites | Distributed microservices |

## Core Highlights

- **Publishing & Drafts** - Full lifecycle with draft previews and soft-delete trash.
- **Media Management** - Secure image uploads with MIME checks and SVG disinfection.
- **Snapshots & Backups** - One-click ZIP/JSON export with automated safety restore snapshots.
- **Theme Engine** - Clean Plates templates with layout inheritance and theme discovery.
- **Security Hardening** - CSRF defense-in-depth, login backoff throttling, and path traversal guards.

---

**Next:** [Complete Guide →](guide.md)