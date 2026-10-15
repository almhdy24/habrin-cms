# Deployment Checklist for Habrin CMS

Follow this checklist when deploying Habrin CMS to a staging or production server.

---

## 1. Web Server Configuration

Ensure your web server has URL rewriting enabled:
- **Apache**: Ensure `mod_rewrite` is enabled and `.htaccess` overrides are allowed (`AllowOverride All`).
- **Nginx**: Forward all non-file requests to `index.php`:
  ```nginx
  location / {
      try_files $uri $uri/ /index.php?$query_string;
  }
  ```

## 2. Directory Permissions

Ensure web-server write permissions on the data and runtime directories:

```bash
chmod +x scripts/deploy.sh
sudo ./scripts/deploy.sh
```

Or manually:

```bash
chmod -R 775 content logs backups assets/uploads
chown -R www-data:www-data content logs backups assets/uploads
```

## 3. Initial Setup

You can either:
- Navigate to `https://your-site.com/installer` in your browser to complete the 3-step setup.
- Or manually create `content/.admin.php` (do NOT commit this file to Git):

```php
<?php
defined('BASE_PATH') || exit;

return [
  'user' => 'admin',
  'pass' => '<bcrypt-hash-here>',
];
```

Generate a secure bcrypt hash:
```bash
php -r "echo password_hash('YourStrongPassword', PASSWORD_BCRYPT);"
```

Create the installation lock:
```bash
touch content/.installed
```

## 4. Production Hardening

- Set `'debug' => false` in `config.php`.
- Ensure HTTPS is enforced; Habrin CMS automatically enables the `Secure` flag on session cookies and sends HSTS headers when HTTPS is active.
- Confirm directory access restrictions: verify that browsing directly to `/content/settings.json`, `/logs/app.log`, or `/backups/` returns HTTP 403 Forbidden.

## 5. Health Verification

Verify that the health check endpoint returns healthy status:

```bash
curl -i https://your-site.com/_health
```

Expected output: `{"status":"ok"}`.

## 6. Runtime Monitoring

Monitor application activity and administrative security events:
- `logs/app.log` for general application errors and notices.
- `logs/audit.log` for administrative logins, logouts, lockouts, and content edits.
