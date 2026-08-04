# Pico JSON CMS

[![Release](https://img.shields.io/github/v/release/almhdy24/pico-json-cms?label=release)](https://github.com/almhdy24/pico-json-cms/releases)
[![Build Status](https://img.shields.io/github/actions/workflow/status/almhdy24/pico-json-cms/ci.yml?branch=main)](https://github.com/almhdy24/pico-json-cms/actions)
[![PHP](https://img.shields.io/badge/php-8.1%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/github/license/almhdy24/pico-json-cms)](LICENSE)

TL;DR: A lightweight, developer-friendly flat-file CMS written in PHP that stores content in JSON files. No database required — ideal for small blogs, documentation sites, and simple business pages.

---

Table of contents

- [About](#about)
- [Features](#features)
- [Quick start](#quick-start)
- [Configuration](#configuration)
- [Themes](#themes)
- [Plugins](#plugins)
- [Administration](#administration)
- [Security & Production notes](#security--production-notes)
- [Development & testing](#development--testing)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Author & Support](#author--support)

---

## About

Pico JSON CMS is a minimalist, flat-file content management system built with PHP and JSON. It focuses on simplicity, transparency, and developer extensibility while providing a familiar admin interface for managing posts, pages, themes, and plugins.

Key principles:

- Keep deployment simple — no RDBMS required
- Store readable, inspectable JSON files in `content/`
- Provide a plugin & theme API for extensibility
- Scale flat-file storage with sharding and indices when needed

---

## Features

- Markdown-based post authoring
- Create / edit / publish / soft-delete posts and pages
- Slug-based friendly URLs and pagination
- Theme system with template fallback
- Plugin system with actions, filters, and settings
- Sharded JSON storage, slug index, and request-level caching for large collections
- Admin dashboard with plugin & theme management
- Basic built-in plugins: SEO, sitemap, analytics (development branch)

---

## Quick start

1. Clone the repository and enter the directory:

```bash
git clone https://github.com/almhdy24/pico-json-cms.git
cd pico-json-cms
```

2. Install dependencies via Composer:

```bash
composer install
```

3. Ensure the content storage directory is writable by the webserver:

```bash
chmod -R 755 content/
# or set proper ownership: chown -R www-data:www-data content/
```

4. Run the web installer by visiting `/installer` once the site is served — for example:

```
http://your-domain.example/installer
```

The installer will initialize site configuration and create the first admin account.

Default development credentials (change immediately in production):

- Username: `admin`
- Password: `secret123`

---

## Configuration

Configuration is stored in `config.php` and the JSON files inside `content/` (e.g., `settings.json`). Key runtime considerations:

- Minimum PHP: 8.1+
- Ensure the PHP JSON extension is enabled
- URL rewriting (Apache/Nginx) is required for pretty URLs
- The `content/` directory must be writable by the web server

Environment-specific overrides can be handled by your webserver or deployment scripts; sensitive values should never be committed to git.

---

## Themes

Themes live under `themes/` and may include layouts, pages, partials, and static assets. A theme typically contains a `theme.json` metadata file describing the theme.

Example `theme.json`:

```json
{
  "name": "My Theme",
  "version": "1.0.0",
  "description": "A custom Pico JSON CMS theme.",
  "author": "Your Name"
}
```

Activate and manage themes from the admin at `/admin/themes`. The theme resolver loads templates from the active theme and falls back to the default theme when needed.

---

## Plugins

Plugins live in `plugins/` and extend the CMS without modifying core files. A minimal plugin structure:

```
plugins/my-plugin/
├── plugin.json
└── my-plugin.php
```

Example `plugin.json` manifest:

```json
{
  "name": "My Plugin",
  "entry": "my-plugin.php",
  "description": "My awesome plugin",
  "version": "1.0.0"
}
```

Register plugin settings and hooks in PHP using the provided API:

```php
use Core\Hooks;
use Core\PluginSettings;

PluginSettings::register('my-plugin', [
    'title' => 'My Plugin Settings',
    'fields' => [
        'api_key' => [
            'type' => 'text',
            'label' => 'API Key',
            'default' => '',
            'sanitize' => 'text',
        ],
    ],
]);

Hooks::add_action('head', function () {
    echo '<meta name="my-plugin" content="active">';
});
```

Manage plugins from `/admin/plugins`.

---

## Example content (post)

A single post stored in the JSON content files may look like this:

```json
{
  "id": "post_0001",
  "title": "Welcome to Pico JSON CMS",
  "slug": "welcome-to-pico-json-cms",
  "content": "# Hello world\nThis is a markdown post.",
  "status": "published",
  "created_at": "2026-01-01T12:00:00Z",
  "updated_at": "2026-01-01T12:00:00Z",
  "meta": {
    "tags": ["intro", "cms"]
  }
}
```

For large sites, posts can be sharded across multiple JSON files and a slug index is used for efficient lookup.

---

## Administration

Admin features include: dashboard, posts, pages, trash, navigation menu, plugin settings, themes, and system information. The admin area includes CSRF protection, authentication checks, and basic login throttling.

Visit `/admin` to sign in.

---

## Security & production notes

This project includes security-focused features, but deploying to production requires additional hardening:

- Always use HTTPS and HSTS
- Run PHP 8.1+ and keep PHP up to date
- Use secure filesystem permissions (avoid world-writable directories)
- Protect admin routes with strong passwords and rate-limiting
- Do not use default development credentials
- Regularly back up the `content/` directory
- Consider additional application-layer caching or reverse proxy for high traffic

---

## Development & testing

Quick checks and scripts shipped with the project:

- PHP syntax check:

```bash
find . -type f -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
```

- Generate test data / seed sharded storage:

```bash
php scripts/seed_sharded.php 100000 10000
```

- Run benchmark script:

```bash
php scripts/benchmark.php 500
```

---

## Roadmap

Planned and in-progress items toward a stable 1.0.0:

- Complete developer documentation and plugin/theme guides
- Stable plugin API and migration guides
- Search functionality and REST API
- Caching improvements and optional user roles

(See `CHANGELOG.md` for release history.)

---

## Contributing

Thanks for considering contributing! Please follow these steps:

1. Fork the repo
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Implement your change and include tests where appropriate
4. Run syntax checks and any tests
5. Commit and push your branch
6. Open a Pull Request describing your change

Commit message guidance: keep messages focused and reference issues where applicable.

Code of conduct: be respectful and constructive in reviews and issues.

---

## License

Pico JSON CMS is licensed under the MIT License. See `LICENSE` for details.

---

## Author & Support

Elmahdi Abdallh — PHP Backend & CMS Developer

- GitHub: https://github.com/almhdy24
- Project: https://github.com/almhdy24/pico-json-cms

If you find the project useful, please star the repository. For questions or support, open an issue.
