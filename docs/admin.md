# Admin Panel Guide

## Authentication
- Access endpoint: `/admin/login`
- Setup: Account is provisioned during the initial installer run. No default credentials exist.
- Lockout: Exceeding 5 failed attempts triggers a 300-second lockout.
- Session: Inactive sessions terminate automatically after 30 minutes.

## Core Workflows
1. **Dashboard**: High-level metrics for published posts, drafts, trashed items, and quick action shortcuts.
2. **Posts**:
   - Manage published posts and drafts.
   - Live search across titles, slugs, and body.
   - Status filtering (All, Published, Draft).
   - Soft-delete moving posts to Trash.
3. **Media Library (`/admin/media`)**:
   - Upload images up to 5MB.
   - Copy Markdown embed codes with a single click.
   - Automatic MIME verification and SVG sanitization.
4. **Backups (`/admin/backup`)**:
   - One-click ZIP/JSON content export.
   - Automated pre-restore safety snapshot.
   - Path-traversal verified restoration.
5. **Static Pages (`/admin/pages`)**:
   - Create custom standalone pages (e.g. `/about`, `/contact`).
6. **Navigation Menu (`/admin/menu`)**:
   - Configure navbar links, order, and labels.
7. **Themes & Plugins**:
   - Select active themes and preview safely.
   - Configure SEO, Google Analytics, and Sitemap options.