# Habrin CMS — Admin Panel & Mobile Guide

> **Publish without the clutter.**

The Habrin CMS administration interface is designed with a modern, mobile-first philosophy: fast, practical, accessible, and completely independent of heavy external JavaScript or CSS frameworks.

---

## 📱 Mobile & Responsive Experience

- **Collapsible Drawer Navigation**:
  - On phones and tablets, the sidebar collapses into an off-screen drawer accessible via the top-bar hamburger toggle (`☰`).
  - Tapping outside the drawer automatically closes it.
- **Sticky Header**:
  - The sticky top bar provides one-tap access to your live site, current section context, and mobile drawer toggle.
- **Touch-Friendly Controls**:
  - All interactive buttons, navigation links, and form inputs meet or exceed accessibility standards ($\ge$ 44px touch targets).
- **Non-Blocking Toasts**:
  - Actions like copying Markdown embed snippets trigger instant feedback notifications via `window.habrinToast` that auto-dismiss without interrupting the publishing flow.

---

## 🔐 Authentication & Session Security

- **Access Endpoint**: `/admin/login`
- **First Admin Setup**: Configured securely during installation via `/installer`. Default or hardcoded credentials are intentionally absent.
- **Login Rate-Limiting**: Progressive backoff delays and a 300-second lockout after 5 consecutive failed attempts.
- **Session Expiry**: Sessions automatically expire after 30 minutes of inactivity and regenerate session IDs on login.

---

## ✍️ Editorial & Content Workflow

1. **Dashboard (`/admin`)**:
   - High-level metric cards for Published Posts, Drafts, Trashed Items, and Media files.
   - Quick navigation shortcuts and recent post activity.

2. **2-Column Post Editor (`/admin/posts/add`, `/admin/posts/edit/{id}`)**:
   - Left column: distraction-free writing canvas for Title and Markdown content.
   - Right column: publishing panel with post status (Published vs. Draft), automated slug preview, and publish button.
   - Real-time word counter and character counter that updates as you write.
   - On mobile screens, the layout gracefully stacks while keeping the action buttons easily accessible.

3. **Post Management (`/admin/posts`)**:
   - Status filter pills: **All**, **Published**, **Drafts**, and **Trash**.
   - Instant search across post titles, slugs, and body content.
   - Soft-delete moving posts to Trash, with one-click restore and permanent purge capabilities.

4. **Media Library (`/admin/media`)**:
   - Drag-and-drop file upload zone.
   - Real-time MIME inspection, extension whitelisting, and SVG sanitization.
   - Click-to-copy Markdown snippet button with instant toast feedback.

5. **Snapshots & Backups (`/admin/backup`)**:
   - One-click ZIP/JSON archive generation.
   - Automatic pre-restoration safety snapshot before restoring any backup.
   - Directory traversal protection validating all zip contents.

6. **Site Architecture**:
   - **Static Pages (`/admin/pages`)**: Create standalone pages like `/about` or `/services`.
   - **Navigation Menu (`/admin/menu`)**: Reorder menu items, set custom labels, and link to internal or external destinations.
   - **Themes & Plugins**: Activate frontend themes (`default`, `cleanblog`, `dark`, `magazine`) and configure plugins (SEO, Sitemap, Analytics).