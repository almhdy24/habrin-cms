# Pico JSON CMS

A simple, fast, and developer-friendly PHP CMS for flat-file content management.

Build small websites without a database. Store content in readable JSON files, write in Markdown, and manage everything through a lightweight admin panel.

[![Release](https://img.shields.io/github/v/release/almhdy24/pico-json-cms?label=release)](https://github.com/almhdy24/pico-json-cms/releases)
[![Build Status](https://img.shields.io/github/actions/workflow/status/almhdy24/pico-json-cms/ci.yml?branch=main)](https://github.com/almhdy24/pico-json-cms/actions)
[![PHP](https://img.shields.io/badge/php-8.1%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/github/license/almhdy24/pico-json-cms)](LICENSE)

<div align="center">

  <p>
    <strong>Minimal setup.</strong>
    <strong>Zero database.</strong>
    <strong>Simple publishing.</strong>
  </p>

</div>

## Why Pico JSON CMS?

Pico JSON CMS is built for people who want a clean, lightweight alternative to large, heavy content management systems.

It is ideal when you need:

- a simple blog or documentation site
- no database setup
- easy file-based content management
- fast deployment to any PHP hosting environment
- full control over content and structure

## Perfect for

- Personal blogs
- Portfolios
- Documentation sites
- Small business websites
- Lightweight content-driven pages

## Not built for

- Large enterprise publishing systems
- E-commerce platforms with heavy catalog logic
- Multi-user SaaS products
- Complex database-centric apps

## Core features

- JSON-based content storage
- Markdown support for article writing
- Simple admin interface
- No database required
- Flat-file architecture
- Theme customization
- Plugin-style extension points
- Easy deployment on standard PHP hosting

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

- Extensibility for small needs
  - Pico JSON CMS — Simple hooks and themes are easy to extend for site-specific needs.
  - WordPress — Powerful plugin ecosystem for many cases, but overkill for small, focused sites.

- Reliability for low-traffic sites
  - Pico JSON CMS — Fewer components means fewer things to break — great for personal blogs, docs, and portfolios.
  - WordPress — More moving parts can create reliability issues unless properly managed.

- AI / automation friendliness
  - Pico JSON CMS — JSON content and Markdown make it easy to integrate AI tooling, content generation, and import/export workflows.
  - WordPress — AI integrations are possible but often depend on plugins and custom work.

When WordPress may be the better choice
- You need a large plugin ecosystem out of the box (e-commerce, membership, complex SEO suites).
- You expect heavy multi-user workflows, editorial roles, or a non-technical client who needs a polished WYSIWYG editor and plugin-driven features.
- You require specialized integrations that already exist as stable WordPress plugins.

Bottom line
- Choose Pico JSON CMS when you want minimal friction, low cost, predictable performance, and full control for small sites.
- Choose WordPress if you need a full-featured ecosystem, lots of prebuilt plugins, or high-scale editorial features.

## See it in action

Live demo:

- https://pico-json-cms.alwaysdata.net/

## Quick start

1. Download or clone this repository.
2. Upload it to any PHP-enabled web server.
3. Visit `/admin/login`.
4. Log in with the default credentials.
5. Start creating and publishing content.

Default access:

- Username: `admin`
- Password: `secret123`

> Change the admin credentials before deploying to production.

## Project structure

The project keeps things simple and transparent:

- `content/` stores your JSON content
- `templates/` or theme files handle presentation
- `admin/` manages content and publishing tools
- `docs/` contains project documentation

## Documentation

- [Docs overview](docs/README.md)
- [Full guide](docs/guide.md)

## Product vision

Pico JSON CMS aims to be the simplest flat-file CMS for people who want clean writing workflows, no database friction, and full control over small websites.

The mission is to make publishing simple again:

- easy to understand
- easy to host
- easy to customize
- easy to maintain

## Security notes

Because this project is designed for lightweight deployment, make sure to:

- change the default admin password
- restrict access to the admin area
- keep your PHP version and server software updated
- review file permissions before production use

## Contributing

Contributions are welcome. If you want to improve the project, open an issue or submit a pull request.

## Author

Created by [Elmahdi Abdallh](https://github.com/almhdy24)

## License

This project is open source and distributed under the MIT license. See the [LICENSE](LICENSE) file for details.

---

Pico JSON CMS is for teams and creators who want a lightweight publishing system that stays simple, transparent, and fast — without the overhead of a traditional CMS.
