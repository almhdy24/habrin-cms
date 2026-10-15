# Plugins in Habrin CMS

**Habrin CMS** includes a lightweight and extensible plugin architecture that allows you to extend CMS capabilities without touching core code. Plugins are PHP files located in the `plugins/` directory and managed via `config.php` or the administrative panel.

---

## How Plugins Work

1. Each plugin resides in its own folder or file inside the `plugins/` directory (e.g. `plugins/markdown/plugin.php`).
2. Plugins can register settings schemas and hook into actions and filters via the `Core\Hooks` API.
3. Errors in plugins are isolated and logged without breaking page execution.

---

## Available Hooks

| Hook Name | Type | Description | Parameters |
|---|---|---|---|
| `admin_notices` | Action | Display alert messages in the admin panel | None |
| `post_content` | Filter | Transform or enrich post Markdown/HTML content | `$content, $post` |
| `before_post` | Action | Trigger logic prior to rendering a post | `$post` array |
| `after_post` | Action | Trigger logic after rendering a post | `$post` array |
| `register_routes` | Action | Register custom HTTP routes on the Router | `$router` instance |

---

## Step 1: Configure Plugins

Edit `config.php` or toggle plugins from `/admin/plugins`:

```php
<?php
return [
    'app_name' => 'Habrin CMS',
    'theme'    => 'default',
    'plugins'  => [
        'markdown',
        'seo',
        'sitemap',
        'analytics',
    ],
];
```

---

## Step 2: Create a Custom Plugin

1. Create a plugin directory or file, for example:
   ```text
   plugins/my-plugin/plugin.php
   ```

2. Register your filters and actions using `Core\Hooks`:

```php
<?php
declare(strict_types=1);

use Core\Hooks;

// Append a reading disclaimer to post content
Hooks::add_filter('post_content', function (string $content, array $post): string {
    return $content . "\n\n---\n*Published with Habrin CMS.*";
});
```

3. Enable the plugin in `config.php`:

```php
'plugins' => [
    'markdown',
    'my-plugin',
],
```

---

## Example: Add an Admin Notice

```php
<?php
use Core\Hooks;

Hooks::add_action('admin_notices', function () {
    echo '<div class="alert alert-info">Notice: System maintenance scheduled for Sunday at 02:00 UTC.</div>';
});
```

---

## Best Practices

1. **Error Isolation**: Habrin CMS catches uncaught plugin exceptions and logs them to `logs/app.log`.
2. **Safe Output**: Always escape dynamic public output using `htmlspecialchars()` or `$this->e()`.
3. **No Database Dependencies**: If your plugin requires state persistence, store structured JSON in `content/`.