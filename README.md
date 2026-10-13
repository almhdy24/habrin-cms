```markdown
# Pico JSON CMS

[![Release](https://img.shields.io/github/v/release/almhdy24/pico-json-cms?label=release)](https://github.com/almhdy24/pico-json-cms/releases)
[![Build Status](https://img.shields.io/github/actions/workflow/status/almhdy24/pico-json-cms/ci.yml?branch=main)](https://github.com/almhdy24/pico-json-cms/actions)
[![PHP](https://img.shields.io/badge/php-8.1%2B-blue)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Stars](https://img.shields.io/github/stars/almhdy24/pico-json-cms?style=social)](https://github.com/almhdy24/pico-json-cms/stargazers)

<div align="center">

  <h2>Simple. Fast. Portable. — A flat-file CMS that feels like writing files.</h2>

  <p>
    Build and publish small websites with readable JSON content, Markdown writing, and a tiny admin panel — no database required.
  </p>

  <p>
    <a href="https://pico-json-cms.alwaysdata.net" style="margin-right:12px">Live demo</a>
    <a href="#quick-start">Get started</a>
  </p>

</div>

---

Why Pico JSON CMS

Pico JSON CMS is designed for people who want a trustworthy, low-friction way to publish content without the complexity of a database-backed CMS. If you value speed, transparency, and control — this is for you.

Key outcomes:
- Launch a small blog or docs site in minutes
- Keep content in human-readable JSON files (easy to back up and audit)
- Edit with Markdown and publish with a minimal admin UI
- Host everywhere that supports PHP — from cheap shared hosts to VPS

## Features

- JSON-based flat-file content storage (no DB)
- Markdown-first editing experience
- Lightweight admin panel for content management
- Themeable templates and simple theming system
- Plugin/hook points for small extensions
- Minimal dependencies and easy hosting

## Hero screenshot

Add a screenshot file (e.g., `assets/screenshot.png`) and replace the link below to show the admin or blog UI:

![Pico JSON CMS screenshot](assets/screenshot.png)

---

## Why choose Pico JSON CMS (short)

- Minimal setup: clone / upload / go. No DB credentials, no migrations.
- Low cost: runs on the most affordable PHP shared hosting plans.
- Performance: tiny footprint and fast responses out of the box.
- Privacy & control: content in files you own and can edit with any editor.
- Dev-friendly: JSON + Markdown makes automation, scripting, and AI tooling straightforward.

## Why Pico JSON CMS is better than WordPress for small sites

If your goal is a small, fast, low-maintenance site, Pico JSON CMS is intentionally simplified. Below are direct comparisons across the things that matter for small projects.

- Setup and deployment
  - Pico JSON CMS — Clone or upload to any PHP host, no DB setup, instant demo content.
  - WordPress — Requires database creation, DB credentials, and often more hosting configuration.

- Hosting cost and complexity
  - Pico JSON CMS — Runs on the cheapest PHP shared hosts, minimal resources.
  - WordPress — Often needs higher-tier hosting for performance, plus more frequent resource spikes.

- Performance and speed
  - Pico JSON CMS — Flat-file JSON + simple PHP = lightweight and fast responses by default.
  - WordPress — Heavier PHP stack and many plugins; typically needs caching and tuning.

- Security surface
  - Pico JSON CMS — Much smaller codebase and fewer moving parts reduces attack surface.
  - WordPress — Large ecosystem and many third-party plugins increase risk and require frequent updates.

- Maintenance and upgrades
  - Pico JSON CMS — Minimal updates required; fewer dependencies to manage.
  - WordPress — Core + plugins + themes all require frequent updates and compatibility checks.

- Privacy and control
  - Pico JSON CMS — Content stored in human-readable JSON files you control; easy to audit and back up.
  - WordPress — Content in a database; backups and migrations are heavier and more error-prone.

- Developer experience and customization
  - Pico JSON CMS — Code-first templates and JSON content make it fast for developers to customize and extend.
  - WordPress — Powerful but often requires learning specific APIs (hooks, filters) and plugin internals.

- AI / automation friendliness
  - Pico JSON CMS — JSON content and Markdown make it easy to integrate AI tooling, content generation, and import/export workflows.
  - WordPress — AI integrations are possible but often depend on plugins and custom work.

Bottom line: Pico JSON CMS is the pragmatic choice for small sites that need low friction and full control. Choose WordPress if you need a large plugin ecosystem, complex integrations, or enterprise editorial workflows.

---

## Quick start

Get going in under 5 minutes.

1. Clone the repo:

```bash
git clone https://github.com/almhdy24/pico-json-cms.git
cd pico-json-cms
```

2. Upload to a PHP host or run locally with PHP built-in server:

```bash
php -S localhost:8000 -t public
```

3. Open `/admin/login` in your browser.

4. Default admin (change immediately):

- username: `admin`
- password: `secret123`

5. Create your first post and publish.

### Docker (optional)

Create a simple Dockerfile or use a PHP image to test locally. Example Docker quick-run:

```bash
docker run --rm -v "$PWD":/var/www/html -p 8080:80 php:8.1-apache
```

### cPanel / shared hosting

- Upload files to your public_html directory
- Ensure `content/` is writable by the web server
- Visit `/admin/login`

---

## Project layout

- `content/` — JSON files containing site content
- `admin/` — admin panel code
- `templates/` — theme templates and views
- `assets/` — images, screenshots, and static files
- `docs/` — documentation and usage guides

---

## Security & deployment checklist

- Change the default admin password immediately.
- Move or protect any sensitive files from public access if necessary.
- Set correct file permissions so `content/` is writable only by the web server.
- Keep PHP and server packages up to date.
- Consider HTTPS and proper headers (HSTS, Content-Security-Policy) for production.

---

## Roadmap

Planned short-term improvements:
- polished admin UX (preview, drafts)
- seed/demo content and one-click deployment guide
- plugin/hook documentation and example plugins
- theme starter kit and example sites
- AI-assisted content helpers (optional plugin)

Contributions welcome — see the section below.

---

## Contributing

We welcome contributors of all kinds:
- bug reports and issues
- pull requests with fixes and improvements
- documentation improvements
- themes and plugins

Please read `CONTRIBUTING.md` (if present) or open an issue to discuss larger changes.

---

## License

MIT — see the [LICENSE](LICENSE) file.

---

## Author & Contact

Built by [Elmahdi Abdallh](https://github.com/almhdy24). For questions or support, open an issue on this repository.

---

Pico JSON CMS — a tiny, purposeful CMS for people who want to publish without friction.
```
