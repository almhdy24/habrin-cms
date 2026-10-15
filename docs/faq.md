# Frequently Asked Questions (FAQ)

## Product & Identity

### Is this Pico CMS?
No. Pico CMS (picocms.org) is an unrelated Markdown-based flat-file CMS created in 2013. Pico JSON CMS is a separate system designed around structured JSON persistence, sharded storage, and a built-in administrative suite.

### Does it require a database like MySQL or PostgreSQL?
No. All content, metadata, static pages, and settings are stored locally in the `content/` directory using atomic JSON writes.

### How does it scale without a database?
Pico JSON CMS implements sharded JSON storage (`content/posts/shard-*.json`) combined with incremental pagination and hashed slug indexing. This enables it to handle thousands of posts without loading the entire collection into memory.

---

## Technical & Troubleshooting

### What PHP version is required?
PHP 8.1.0 or higher. Strict typing and modern PHP 8 features are utilized throughout the codebase.

### What should I check if uploads fail?
1. Ensure `assets/uploads/` directory is writable by your web server (`chmod -R 775 assets/uploads`).
2. Verify the file size is under 5MB.
3. Verify the file extension and MIME type match allowed image formats (`jpg`, `png`, `gif`, `webp`, `svg`).

### How do I reset a forgotten administrator password?
Because password hashes are stored in `content/.admin.php`, you can generate a new bcrypt hash using the command line:

```bash
php -r "echo password_hash('YourNewPassword', PASSWORD_BCRYPT);"
```

Replace the `'pass'` hash value inside `content/.admin.php`.

### How do I run tests?
Run `php vendor/bin/phpunit` from the project root.