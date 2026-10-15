# 🔒 Habrin CMS — CORE FREEZE POLICY

**Effective starting from v0.2.0 (Preserved in Habrin CMS v1.0.0+)**

This document defines the **Core Freeze policy** for Habrin CMS (formerly Pico JSON CMS).
Its goal is to provide **API stability**, **upgrade safety**, and **long-term confidence** for users, theme developers, and plugin authors.

---

## 🎯 Purpose of Core Freeze

Habrin CMS is designed to be:

- Minimal
- Predictable
- Long-term maintainable

Once the core is frozen:
- Existing sites **must not break**
- Plugins and themes can rely on stable APIs
- Updates focus on **features**, not rewrites

---

## 🧊 What “Frozen” Means

A **frozen component** guarantees:

- No breaking changes to public APIs
- No removal or renaming of methods
- No behavior changes without a major version bump
- Bug fixes only (no logic redesign)

Frozen does **not** mean “no improvements” — only **no breaking ones**.

---

## ✅ FROZEN (Stable Core)

The following parts are considered **stable and frozen** starting from **v0.2.0** and fully locked in **v1.0.0**.

### 1️⃣ Core Architecture

- `core/App.php`
- `core/Router.php`
- `core/Controller.php`
- `core/Model.php`
- `core/View.php`
- `core/Auth.php`
- `core/Session.php`
- `core/Hooks.php`

Guarantees:
- Public methods will not change
- Method signatures are stable
- Routing and lifecycle behavior is fixed

---

### 2️⃣ Base Controller API

Stable methods include (but are not limited to):

- `$this->view()`
- `$this->redirect()`
- `$this->input()`
- `$this->isPost()`
- `$this->model()`
- `$this->flash()`

Controllers **may add new methods**, but existing ones will not be removed or altered.

---

### 3️⃣ Base Model API

The following behaviors are frozen:

- JSON file-backed storage
- Atomic writes via temp files
- Public methods:
  - `all()`
  - `find()`
  - `save()`

No schema enforcement is imposed by the core.

---

### 4️⃣ Routing Rules

- URL → Controller → Method mapping is frozen
- Slug-based post routing (`/post/{slug}`) is stable
- Admin routing structure (`/admin/...`) is fixed

New routes may be added, but existing ones will not change behavior.

---

### 5️⃣ Installer Output Contract

The installer **must always generate**:

- `content/settings.json`
- `content/.admin.php`
- `content/.installed`

Field names inside these files are **backward-compatible** once introduced.

---

## 🧪 EXPERIMENTAL (May Change)

The following areas are **not frozen** and may evolve:

---

### 🔧 Plugins System

- Plugin discovery
- Plugin structure
- Plugin helpers
- Plugin lifecycle hooks

> Plugins are supported, but the API may be refined until v1.0.0.

---

### 🎨 Themes System

- Theme directory structure
- Asset handling
- Admin UI layout
- CSS strategy

Themes should avoid depending on internal PHP logic.

---

### 🧱 Admin UI / UX

- Layout
- Icons
- CSS structure
- Minor HTML markup

Admin routes remain stable, but visuals may evolve.

---

### ⚙️ Helpers & Utilities

- Global helper functions
- Pagination helpers
- Utility wrappers

These may change if better abstractions are found.

---

## 🚫 What Is Explicitly NOT Frozen

- Internal private methods
- Internal class properties
- Experimental helper functions
- Dev-only features
- Anything marked `@internal`

---

## 🧭 Versioning Rules

Habrin CMS follows **Semantic Versioning (SemVer)**:

- **PATCH** (`0.2.x`)  
  → Bug fixes only, no behavior change

- **MINOR** (`0.x.0`)  
  → New features, backward-compatible

- **MAJOR** (`1.0.0`)  
  → Breaking changes (only if absolutely necessary)

After **v1.0.0**, breaking changes require:
- Clear migration path
- Upgrade documentation
- Major version bump

---

## 🔐 Long-Term Vision

- `v0.2.x` → Stabilization & polish
- `v0.9.x` → Feature complete
- `v1.0.0` → Fully frozen core, long-term support
- `v1.x`   → Safe evolution without breaking sites

---

## 📝 Final Note

This freeze policy is a **promise**:
> *Your site will continue working.*

If something must change, it will be:
- Documented
- Versioned
- Optional

---

**Habrin CMS**  
Minimal. Predictable. Built to last. 🚀