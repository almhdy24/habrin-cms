# Frequently Asked Questions (FAQ)

> **Publish without the clutter.**

## Product & Identity

### What is Habrin CMS?
Habrin CMS is a lightweight, mobile-friendly flat-file content management system designed around structured JSON persistence, Markdown content, and an intuitive administrative suite. Its brand philosophy is simple: **publish without the clutter** — offering clean typography, subtle borders, accessible contrast, and zero unnecessary dependencies or gradients.

### Is this related to Pico CMS?
No. Pico CMS (picocms.org) is an unrelated Markdown-based flat-file CMS from 2013. Habrin CMS is an independent, modern platform featuring sharded JSON storage, mobile drawer admin UX, built-in media management, automated backups, and strict security hardening.

### Does it require a database like MySQL, PostgreSQL, or SQLite?
No. All content, metadata, pages, menus, and site configurations are stored locally in the `content/` directory using atomic writes and file locking.

### How does it scale without a database?
Habrin CMS uses sharded JSON storage (`content/posts/shard-*.json`) alongside incremental pagination and hashed slug indexing. This allows it to handle thousands of posts efficiently without loading the entire collection into memory.

---

## Technical & Troubleshooting

### What PHP version is required?
PHP 8.1.0 or higher. Strict typing and modern PHP 8 features are utilized throughout the codebase.

### What should I check if uploads fail?
1. Ensure the `assets/uploads/` directory is writable by your web server (`chmod -R 775 assets/uploads`).
2. Verify the file size does not exceed 5MB.
3. Verify the file extension and MIME type match allowed formats (`jpg`, `jpeg`, `png`, `gif`, `webp`, `svg`).

### How do I reset a forgotten administrator password?
Password hashes are stored in `content/.admin.php`. You can generate a new bcrypt hash from the command line:

```bash
php -r "echo password_hash('YourNewPassword', PASSWORD_BCRYPT);"
```

Replace the `'pass'` hash value inside `content/.admin.php`.

### How do I run the automated tests?
Run `php vendor/bin/phpunit` from the project root.