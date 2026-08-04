Pico JSON CMS

Pico JSON CMS is a lightweight, flat-file Content Management System built with PHP and JSON. It is designed for developers who want a simple, database-free CMS that is easy to deploy, inspect, extend, and maintain.

The project uses JSON files as its primary data store and provides an extensible architecture with themes, plugins, hooks, filters, an administrative dashboard, Markdown content, and scalable sharded post storage.

«Status: Active development — pre-1.0
Current development version: "0.9.0-dev"
Minimum PHP: "8.1"
Storage: Flat-file JSON
License: MIT»

---

✨ Features

📝 Content Management

- Create, edit, publish, and delete posts
- Markdown-based post authoring
- Soft-delete and Trash functionality
- Restore or permanently delete trashed content
- Static pages such as About and Contact
- Public navigation menu management
- Pagination for large post collections
- Slug-based post URLs

⚡ Scalable Flat-File Storage

Pico JSON CMS does not require MySQL or another database server.

For larger installations, posts can be stored using a sharded JSON architecture:

- Posts distributed across multiple shard files
- Configurable records per shard
- Slug index for fast post lookup
- Pagination indexes
- Metadata for efficient post and trash counts
- Request-level caching
- Atomic writes with file locking

The architecture is designed to make flat-file storage practical for large datasets while preserving the simplicity of JSON.

«Performance should always be benchmarked against your actual hardware, filesystem, PHP version, and workload.»

🎨 Theme System

Pico JSON CMS supports multiple themes.

Themes can provide:

- Layouts
- Pages
- Partials
- CSS
- JavaScript
- Images and other assets
- "theme.json" metadata

The CMS supports an active theme with fallback resolution to the default theme for templates that are not provided by the active theme.

Current themes include:

Theme| Description
Default| Clean, modern, responsive default interface
CleanBlog| Minimal and readable blogging experience
Magazine| Editorial-inspired design for articles and long-form content
Dark| Minimal dark interface optimized for low-light reading

Theme management is available from:

/admin/themes

Theme activation is handled through the administrative interface.

🧩 Plugin System

Plugins extend Pico JSON CMS without modifying the core application.

The plugin architecture supports:

- Plugin discovery
- Plugin manifests
- Enable/disable management
- Actions
- Filters
- Plugin settings
- Schema-driven settings fields
- Input sanitization
- Error isolation

Example plugin structure:

plugins/
└── my-plugin/
    ├── plugin.json
    └── my-plugin.php

Example manifest:

{
  "name": "My Plugin",
  "entry": "my-plugin.php",
  "description": "My awesome plugin",
  "version": "1.0.0"
}

A plugin can register functionality through the CMS hook system.

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

🔌 Built-in Plugins

The development branch currently includes plugins for functionality such as:

- SEO metadata and Open Graph support
- XML sitemap generation
- Analytics integration

Plugin availability may vary between releases.

🛠 Administration

The administrative panel provides management interfaces for:

- Dashboard
- Posts
- Add/Edit Post
- Trash
- Static Pages
- Navigation Menu
- Plugins
- Plugin Settings
- Themes
- Site Settings
- System Information

The admin interface includes CSRF protection and authentication controls.

🔒 Security

Security-related functionality includes:

- CSRF protection for state-changing requests
- Password hashing using PHP's "password_hash()"
- Session regeneration during authentication
- Login throttling
- Authentication and authorization checks
- Input validation
- Output escaping
- Theme path validation
- Plugin path validation
- Directory traversal protection
- Atomic file writes
- File locking
- Secure HTTP headers where supported

Security is an ongoing development priority. Deployments should still use HTTPS, secure server configuration, and appropriate filesystem permissions.

---

🚀 Installation

Requirements

- PHP "8.1" or newer
- Composer
- PHP JSON extension
- Apache or Nginx
- URL rewriting support

The application is designed to run without a relational database.

---

1. Clone the Repository

git clone https://github.com/almhdy24/pico-json-cms.git
cd pico-json-cms

2. Install Dependencies

composer install

3. Configure Writable Storage

The CMS requires its content storage directory to be writable by the web server.

For example:

chmod -R 755 content/

Depending on your hosting environment, you may need different ownership or permissions.

Avoid using "777" unless absolutely necessary.

4. Run the Installer

Open the installer in your browser:

/installer

For example:

http://your-domain.example/installer

The installer initializes the CMS and creates the initial administrator account and site configuration.

---

🎨 Themes

Themes live in:

themes/

A typical theme can contain:

themes/my-theme/
├── theme.json
├── layouts/
├── pages/
├── partials/
└── assets/
    ├── css/
    ├── js/
    └── images/

A basic "theme.json":

{
  "name": "My Theme",
  "version": "1.0.0",
  "description": "A custom Pico JSON CMS theme.",
  "author": "Your Name"
}

The active theme is configured through the CMS configuration system.

Theme management is available at:

/admin/themes

The theme resolver attempts to load templates from the active theme first and can fall back to the default theme when an active theme does not provide a required template.

This allows administrative templates and shared functionality to remain available even when a public theme only implements frontend templates.

---

🧩 Plugins

Plugins live in:

plugins/

A plugin normally contains:

plugins/my-plugin/
├── plugin.json
└── my-plugin.php

Plugins can interact with the CMS using actions and filters.

Example:

Hooks::add_action('head', function () {
    echo '<meta name="my-plugin" content="active">';
});

Plugin settings can be registered through the settings API and exposed through the administrative interface.

Manage installed plugins from:

/admin/plugins

---

📂 Project Structure

pico-json-cms/
├── README.md
├── CHANGELOG.md
├── LICENSE
├── composer.json
├── config.php
├── index.php
├── .htaccess
│
├── core/
│   ├── App.php
│   ├── Router.php
│   ├── View.php
│   ├── Controller.php
│   ├── Model.php
│   ├── Hooks.php
│   ├── Session.php
│   ├── Auth.php
│   ├── PluginLoader.php
│   ├── PluginSettings.php
│   ├── PluginSettingsRegistry.php
│   └── ThemeTemplateResolver.php
│
├── controllers/
│   ├── HomeController.php
│   ├── AdminController.php
│   ├── AdminPagesController.php
│   ├── AdminMenuController.php
│   ├── AdminPluginsController.php
│   ├── AdminThemesController.php
│   └── ...
│
├── models/
│   ├── PostsModel.php
│   ├── SettingsModel.php
│   ├── PagesModel.php
│   ├── MenuModel.php
│   ├── ThemesModel.php
│   └── ...
│
├── themes/
│   ├── default/
│   ├── cleanblog/
│   ├── magazine/
│   └── dark/
│
├── plugins/
│   ├── seo/
│   ├── sitemap/
│   └── analytics/
│
├── content/
│   ├── posts.json
│   ├── posts.index.json
│   ├── settings.json
│   ├── pages.json
│   ├── menu.json
│   ├── plugins.json
│   ├── plugin-settings/
│   └── .installed
│
└── scripts/
    ├── seed.php
    ├── seed_sharded.php
    ├── benchmark.php
    └── migrate_to_sharded.php

The exact structure may change as the project evolves.

---

⚡ Performance

Pico JSON CMS includes several mechanisms intended to improve performance with larger content collections:

Sharded Storage

Large post collections can be distributed across multiple JSON files instead of a single massive file.

Slug Index

Post slug lookups can use an index rather than scanning every post.

Pagination Indexes

Large collections can be paginated without loading the entire dataset into memory.

Metadata

Frequently requested statistics can be stored separately to avoid repeatedly scanning all content.

Request-Level Caching

Repeated reads during a single request can reuse cached model data.

Atomic Writes

Data writes can use temporary files and atomic replacement to reduce the risk of partially written JSON files.

For large installations, always benchmark the application on the target environment.

---

🧪 Development & Testing

PHP Syntax Check

Run a syntax check against PHP files outside the Composer vendor directory:

find . \
  -type f \
  -name "*.php" \
  -not -path "./vendor/*" \
  -print0 | \
  xargs -0 -n1 php -l

Generate Test Data

For sharded storage testing:

php scripts/seed_sharded.php 100000 10000

The exact arguments depend on the current implementation of the seeder.

Benchmark

php scripts/benchmark.php 500

Benchmark results depend on:

- PHP version
- Storage device
- Filesystem
- Hosting environment
- Number of posts
- Request concurrency
- Cache state

---

🛣️ Roadmap

The project is currently evolving toward a stable "1.0.0" release.

Planned areas include:

- [ ] Complete developer documentation
- [ ] Theme development documentation
- [ ] Plugin API documentation
- [ ] Upgrade and migration guides
- [ ] Stable plugin API
- [ ] More built-in plugins
- [ ] Search functionality
- [ ] Caching improvements
- [ ] Social sharing integrations
- [ ] REST API
- [ ] Optional user roles and permissions

The roadmap is subject to change.

---

🤝 Contributing

Contributions, bug reports, and ideas are welcome.

1. Fork the repository.
2. Create a feature branch.

git checkout -b feature/amazing-feature

3. Make your changes.
4. Run syntax checks and tests.
5. Commit your changes.

git commit -m "Add amazing feature"

6. Push your branch.

git push origin feature/amazing-feature

7. Open a Pull Request.

Please keep changes focused and maintain the project's lightweight architecture.

---

📄 License

Pico JSON CMS is licensed under the MIT License.

See the "LICENSE" file for the complete license text.

---

👨‍💻 Author

Elmahdi Abdallh

PHP Backend & CMS Developer

- GitHub: https://github.com/almhdy24
- Project: https://github.com/almhdy24/pico-json-cms

---

⭐ Support the Project

If Pico JSON CMS is useful to you, consider starring the repository on GitHub.

Built with PHP, JSON, and a focus on simplicity.