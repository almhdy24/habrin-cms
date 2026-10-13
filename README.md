# Pico JSON CMS

A lightweight, developer-friendly flat-file CMS built in PHP.

Store your content in readable JSON, write in Markdown, and publish your site with a simple admin panel — without a database.

[![Release](https://img.shields.io/github/v/release/almhdy24/pico-json-cms?label=release)](https://github.com/almhdy24/pico-json-cms/releases)
[![Build Status](https://img.shields.io/github/actions/workflow/status/almhdy24/pico-json-cms/ci.yml?branch=main)](https://github.com/almhdy24/pico-json-cms/actions)
[![PHP](https://img.shields.io/badge/php-8.1%2B-blue)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Stars](https://img.shields.io/github/stars/almhdy24/pico-json-cms?style=social)](https://github.com/almhdy24/pico-json-cms/stargazers)

<div align="center">

  <h2>Simple. Fast. Open.</h2>

  <p>
    Pico JSON CMS is a modern flat-file CMS for people who want a clean, lightweight, and fully controllable publishing workflow.
  </p>

  <p>
    <a href="https://pico-json-cms.alwaysdata.net">Live Demo</a> ·
    <a href="#quick-start">Quick Start</a> ·
    <a href="#features">Features</a> ·
    <a href="#contributing">Contributing</a>
  </p>

</div>

---

## Why build another CMS?

Most small websites do not need the overhead of a large database-driven platform.

Pico JSON CMS is built for:
- personal blogs
- documentation sites
- portfolios
- small business websites
- lightweight content-driven pages

It gives you the simplicity of file-based content management without sacrificing a clean admin experience.

---

## What makes it different?

Pico JSON CMS keeps things intentionally lean:

- No database required
- JSON content files instead of complex CMS storage
- Markdown-based writing
- Easy deployment to PHP hosting
- Small codebase and less maintenance overhead
- Good fit for simple websites and developer workflows

---

## Features

- JSON-based flat-file content storage
- Markdown-friendly authoring
- Lightweight admin panel
- No database setup required
- Easy deployment on PHP servers
- Simple theme customization
- Extensible architecture for plugins and hooks
- Clear and readable project structure
- Hardened admin auth and CSRF protection
- Audit logging for admin actions
- CSP nonce support and production security headers
- Health endpoint for deployment checks

---

## Why Pico JSON CMS is better than WordPress for small sites

If your project is small, simple, and focused, Pico JSON CMS is often the better choice.

| Area | Pico JSON CMS | WordPress |
|---|---|---|
| Setup | Very simple | More setup and DB configuration |
| Hosting | Lightweight and affordable | Usually heavier and more expensive |
| Performance | Fast by default | Often needs optimization and caching |
| Maintenance | Fewer moving parts | More plugins/themes/core updates |
| Security surface | Smaller and simpler | Larger and more complex |
| Content storage | Human-readable JSON files | Database-based |
| Best for | Blogs, docs, portfolios, small sites | Large ecosystems and plugin-driven sites |

Choosing Pico JSON CMS makes sense when you want:
- less complexity
- lower hosting costs
- better control
- a cleaner, simpler development workflow

---

## Quick Start

### 1. Clone the repository

```bash
git clone https://github.com/almhdy24/pico-json-cms.git
cd pico-json-cms
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Start the app locally

```bash
php -S localhost:8000
```

### 4. Open the admin panel

Visit:

```text
http://localhost:8000/admin/login
```

### 5. Configure secure admin credentials

This project should not be used with the default password in production.

Create a secure `content/.admin.php` file with a bcrypt-hashed password:

```php
<?php
return [
    'user' => 'admin',
    'pass' => '$2y$10$YOUR_BCRYPT_HASH_HERE',
];
```

Generate a hash locally:

```bash
php -r "echo password_hash('YourStrongPassword', PASSWORD_DEFAULT), PHP_EOL;"
```

Then log in with your chosen username and password.

---

## Production security features

Pico JSON CMS includes several built-in protections for deployment:

- CSRF validation on admin actions
- login throttling and lockout protection
- session timeout handling for admin users
- secure session cookie defaults
- audit logging for admin events
- Content Security Policy headers with nonce support
- health endpoint checks for deployment validation
- permission checks for writable content/log directories

### Health endpoint

A health endpoint is available at:

```text
/_health
```

It is protected by default. If you set a health key in configuration, call it with:

```bash
curl -H "X-Health-Key: your-very-strong-secret" https://your-domain/_health
```

If no key is configured, the endpoint is restricted to localhost by default.

---

## Project Structure

```text
pico-json-cms/
├── content/        # JSON content files
├── controllers/    # app controllers
├── core/           # core framework classes
├── docs/           # documentation
├── logs/           # app and audit logs
├── models/         # data models
├── plugins/        # plugin modules
├── themes/         # themes
├── assets/         # static assets
├── composer.json
├── config.php
├── functions.php
├── index.php
├── README.md
├── docs/DEPLOY.md
└── ...
```

---

## Documentation

- [Docs Overview](docs/README.md)
- [Deployment Guide](docs/DEPLOY.md)
- [Full Guide](docs/guide.md)

---

## Security Notes

Before deploying to production:

- replace the default admin password and use a hashed credential file
- restrict access to admin routes
- keep PHP and server software up to date
- review file permissions
- enable HTTPS in production
- set a strong `health_key` if you use the health endpoint remotely
- rotate log files regularly

---

## Roadmap

We aim to keep the project focused, useful, and easy to maintain.

Planned improvements include:
- better admin UX
- more theme examples
- plugin documentation
- easier setup guides
- AI-friendly content workflows
- improved content import/export tools

---

## Contributing

Contributions are welcome.

You can help by:
- reporting bugs
- improving docs
- suggesting features
- submitting PRs
- creating themes or plugins

If you are planning a larger change, it’s a good idea to open an issue first so the project direction stays clear and consistent.

---

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.

---

## Author

Elmahdi Abdallh

GitHub: https://github.com/almhdy24
