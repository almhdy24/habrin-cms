# Deployment checklist for Pico JSON CMS

1. Create the admin credentials file (do NOT commit to git)

```php
<?php
return [
  'user' => 'admin',
  'pass' => '<bcrypt-hash-here>',
];
```

Generate a bcrypt hash locally: `php -r "echo password_hash('YourStrongPassword', PASSWORD_DEFAULT).PHP_EOL;"`

Place the file at `content/.admin.php` and ensure it is owned by the web server user.

2. Run permission script

```bash
chmod +x scripts/deploy.sh
sudo ./scripts/deploy.sh
```

3. Verify health endpoint

Open `https://your-site/_health` (or http if running locally) and confirm `status: ok`.

4. Enable HTTPS and HSTS in production

Ensure your web server forces HTTPS and that App::$config['debug'] is false to enable HSTS.

5. Monitor logs

- `logs/app.log` for application logs
- `logs/audit.log` for admin audit events

