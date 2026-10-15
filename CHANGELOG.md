Changelog

All notable changes to Habrin CMS are documented in this file.

This project follows the principles of "Keep a Changelog" (https://keepachangelog.com/) and "Semantic Versioning" (https://semver.org/).

[1.0.0] — 2026-10-15

Official stable release of Habrin CMS: featuring modern product branding, a responsive mobile-first administrative design system, comprehensive security hardening, publishing workflows, and automated test coverage.

Added

- Branding & Identity: Complete transformation into Habrin CMS with custom layered isometric SVG mark, updated package descriptor (`habrin/habrin`), and session management (`habrin_session`).
- Design System & Mobile UX: Collapsible off-canvas drawer navigation with mobile top bar and hamburger toggle.
- Design System & Mobile UX: 2-column post editor with live word and character counters, slug preview, and responsive mobile collapse.
- Design System & Mobile UX: Non-blocking animated toast notification engine (`window.habrinToast`) for instant feedback.
- Design System & Mobile UX: High-contrast dashboard metrics, categorized sidebar navigation (`Content`, `Structure`, `Maintenance`), and $\ge$ 44px touch targets.
- Security: Strict CSRF token verification across all administrative state-changing actions.
- Security: Removed default admin credentials; installer mandates custom strong passwords with default-password rejection.
- Security: Direct access denial `.htaccess` protection for `content/`, `logs/`, `backups/`, and script execution prevention in `assets/uploads/`.
- Security: Path traversal validation in PluginLoader manifests and theme preview handlers.
- Security: Strict MIME validation, extension whitelisting, file size limits, and SVG XSS sanitization (scripts/iframes/handlers stripped).
- Publishing: Drafts workflow with status indicators, draft counter, and authenticated administrator preview banner.
- Publishing: Sharded post search across titles, slugs, and content with pagination.
- Media: Secure Media Library with file upload, preview, markdown snippet copying, and deletion.
- Backups: One-click ZIP/JSON export and restoration with automatic pre-restore safety snapshots and zip traversal prevention.
- Tests: Comprehensive PHPUnit test suite with 37 tests and 112 assertions covering Router, Auth, Session, Posts, Media, Backups, and Security.
- Core: Implemented `all()` on `PostsModel` to comply with frozen `Core\Model` contract in `CORE_FREEZE.md`.

---

[0.9.0-dev] — 2026-08-04

«Unreleased development version.»

Added

Core

- Added sharded JSON storage for large post collections.
- Added post slug indexing for faster slug-based lookups.
- Added pagination indexes for large post collections.
- Added metadata storage for efficient post and trash counts.
- Added request-level model caching.
- Added atomic file-writing mechanisms with locking.
- Added theme discovery and metadata support through "theme.json".
- Added theme activation through the administration panel.
- Added custom theme template resolution.
- Added fallback resolution to the default theme.
- Added plugin settings infrastructure.
- Added schema-driven plugin settings fields.
- Added plugin management through the administration panel.
- Added unified action and filter hooks.
- Added priority support for hooks.
- Added plugin error isolation.

Administration

- Added admin dashboard functionality.
- Added paginated post management.
- Added post creation and editing.
- Added Markdown editing support.
- Added word-count functionality to the post editor.
- Added soft-delete and Trash management.
- Added static page management.
- Added navigation menu management.
- Added plugin management.
- Added plugin settings management.
- Added theme management.
- Added theme activation controls.
- Added system information pages.
- Added site settings management.
- Improved administrative navigation.

Themes

- Added Default theme improvements.
- Added CleanBlog theme.
- Added Magazine theme.
- Added Dark theme.
- Added theme metadata through "theme.json".
- Added support for theme-specific layouts, pages, partials, and assets.
- Added fallback behavior for templates missing from the active theme.

Plugins

- Added SEO plugin functionality.
- Added sitemap generation.
- Added analytics integration.
- Added plugin configuration and settings support.

---

Changed

- Changed post storage architecture from a single large JSON collection toward sharded storage.
- Changed post listing to use pagination-oriented data structures.
- Changed post lookup to support indexed slug resolution.
- Changed the admin interface to use a sidebar-based navigation structure.
- Changed theme resolution to support active-theme and default-theme fallback.
- Improved plugin loading and error handling.
- Improved CSRF validation across administrative state-changing operations.
- Improved flash-message presentation.
- Improved Markdown editing experience.
- Replaced heavier frontend styling dependencies with lightweight custom CSS where applicable.

---

Fixed

- Fixed admin post count handling when pagination data was used instead of total counts.
- Fixed CSRF validation issues in administrative POST requests.
- Fixed theme activation configuration handling.
- Fixed missing administrative templates when the active frontend theme does not contain admin templates.
- Fixed boolean setting parsing for values such as ""0"".
- Improved login throttling behavior after repeated failed authentication attempts.
- Fixed various issues related to large JSON collections and repeated file reads.
- Fixed theme template path validation and directory traversal concerns.

---

Removed

- Removed reliance on loading the complete post collection through the legacy "all()" workflow where sharded pagination is available.
- Removed Bulma-based styling from areas replaced with custom lightweight CSS.
- Replaced Parsedown-based Markdown processing with the project's current Markdown/CommonMark implementation.

---

[0.8.0]

Added

- Added plugin management.
- Added plugin manifests.
- Added plugin enable/disable functionality.
- Added plugin settings.
- Added schema-driven plugin settings.
- Added SEO functionality.
- Added sitemap functionality.
- Added analytics integration.

Changed

- Improved plugin discovery.
- Improved plugin loading order.
- Introduced a unified hook architecture.
- Improved administrative plugin navigation.

Fixed

- Fixed plugin loading errors.
- Improved plugin failure isolation.
- Improved CSRF validation for plugin settings.

---

[0.7.0]

Added

- Added initial theme management architecture.
- Added theme discovery.
- Added theme activation.
- Added "ThemesModel".
- Added "AdminThemesController".
- Added theme metadata support.
- Added CleanBlog theme.
- Added Magazine theme.
- Added Dark theme.
- Added theme screenshot metadata support.

Changed

- Improved configuration updates through the application configuration layer.
- Extended router handling for administrative theme routes.

Fixed

- Fixed theme management routing issues.
- Improved theme activation handling.

---

[0.6.0]

Added

- Added sharded JSON post storage.
- Added support for testing large post collections.
- Added large dataset seeding tools.
- Added slug indexing.
- Added pagination indexes.
- Added metadata for efficient collection statistics.
- Added benchmark tooling.

Changed

- Changed post storage architecture to support sharding.
- Changed administrative post listing to use pagination-oriented data.

Fixed

- Improved performance when working with large post collections.
- Reduced unnecessary full-dataset reads.

---

[0.5.0]

Added

- Added plugin system foundation.
- Added hooks and filters.
- Added hook priorities.
- Added global hook helper functions.
- Added plugin discovery through manifests.
- Added plugin loading infrastructure.

Changed

- Unified application extensibility around the hook system.
- Improved separation between core functionality and plugins.

---

[0.4.0]

Added

- Added static pages.
- Added About and Contact-style page support.
- Added public menu management.
- Added page management in the admin panel.
- Added menu management in the admin panel.
- Added "PagesModel".
- Added "MenuModel".

Changed

- Extended routing to support static page URLs.
- Added Pages and Menu sections to the admin navigation.

---

[0.3.0]

Added

- Added admin dashboard improvements.
- Added paginated post management.
- Added post creation.
- Added post editing.
- Added EasyMDE Markdown editor integration.
- Added word counting.
- Added Trash functionality.
- Added soft deletion.
- Added automatic trash cleanup.
- Added site settings management.

Changed

- Restructured administrative post management.
- Improved separation between post operations and presentation.

Fixed

- Improved post editing and deletion workflows.
- Improved administrative navigation.

---

[0.2.0]

Added

- Added web-based installer.
- Added first-run installation workflow.
- Added installation lock mechanism.
- Added administrator account setup.
- Added session management.
- Added CSRF protection.
- Added authentication and authorization.
- Added login throttling.

Changed

- Removed hard-coded administrator credentials.
- Improved application error handling.
- Improved session security.

---

[0.1.0]

Added

- Initial public development release.
- JSON-based content storage.
- Basic administrative panel.
- Basic theme support.
- Markdown content rendering.
- Homepage.
- Single post pages.
- Initial routing system.

---

Roadmap

v1.0.0

Planned areas for the first stable release:

- [ ] Complete public documentation.
- [ ] Theme development guide.
- [ ] Plugin development guide.
- [ ] Plugin API stabilization.
- [ ] Upgrade and migration documentation.
- [ ] More built-in plugins.
- [ ] Search functionality.
- [ ] Caching improvements.
- [ ] Social sharing features.
- [ ] REST API.
- [ ] Optional roles and permissions.

---

Versioning

Habrin CMS uses semantic versioning:

MAJOR.MINOR.PATCH

For example:

1.2.3
│ │ └── Patch: bug fixes
│ └──── Minor: backward-compatible features
└────── Major: breaking changes

Until "1.0.0", development releases may contain architectural changes and are not considered API-stable.

---

Changelog Categories

- Added — New functionality.
- Changed — Changes to existing functionality.
- Deprecated — Functionality scheduled for removal.
- Removed — Removed functionality.
- Fixed — Bug fixes.
- Security — Security-related changes.