<?php
/**
 * Habrin CMS — Admin Controller
 *
 * Fully compatible with sharded JSON storage.
 * - No all() calls – uses metadata counts and paginated indexes.
 * - Uses PostsModel::nextId() for atomic ID generation.
 * - Uses PostsModel::generateSlug() for unique slugs.
 * - Trash cleanup is infrequent and uses trashed() scanning.
 */

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use Core\App;
use Core\Auth;
use Core\Audit;
use Models\PostsModel;
use Models\SettingsModel;

final class AdminController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS   = 300;
    private const TRASH_DAYS        = 30;
    private const LOGIN_BACKOFF_MAX = 5; // seconds

    private PostsModel $posts;
    private SettingsModel $settings;

    public function __construct()
    {
        parent::__construct();
        $this->guard();

        $this->posts = $this->model(PostsModel::class);
        $this->settings = $this->model(SettingsModel::class);

        $this->posts->clearCache();

        View::share([
            'trashCount' => $this->posts->trashedCount(),
            'draftCount' => $this->posts->draftsCount(),
        ]);
    }

    /* =====================================================
     * Authentication
     * ===================================================== */

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin/dashboard');
        }

        $throttle = Session::get('login_throttle', [
            'attempts'     => 0,
            'locked_until' => null,
        ]);

        if ($throttle['locked_until'] && time() < $throttle['locked_until']) {
            $this->view('admin/login', [
                'flash' => [[
                    'type'    => 'error',
                    'message' => 'Too many attempts. Please wait.',
                ]],
                'csrf_token' => Session::csrfToken(),
            ]);
            return;
        }

        if ($this->isPost()) {
            // validate CSRF early
            $this->validateCsrf();
            $this->processLogin($throttle);
            return;
        }

        $this->view('admin/login', [
            'flash' => $this->getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    private function processLogin(array $throttle): void
    {
        $username = trim((string) $this->input('username', ''));
        $password = (string) $this->input('password', '');

        // Load admin credentials strictly from content/.admin.php
        $adminPath = App::path('content', '.admin.php');
        if (file_exists($adminPath)) {
            $adminCfg = require $adminPath;
            $adminUser = is_array($adminCfg) ? (string) ($adminCfg['user'] ?? '') : '';
            $adminPassHash = is_array($adminCfg) ? (string) ($adminCfg['pass'] ?? '') : '';
        } else {
            $adminUser = '';
            $adminPassHash = '';
        }

        $valid = false;
        if ($adminUser !== '') {
            // Timing-safe compare for username; password_verify handles hashing safely
            $userMatch = hash_equals((string) $adminUser, (string) $username);
            if ($userMatch && is_string($adminPassHash) && $adminPassHash !== '') {
                $valid = password_verify($password, $adminPassHash);
            }
        }

        if ($valid) {
            Session::remove('login_throttle');
            Auth::login();
            // Store actor identity for audit logs
            Session::set('user', $username);
            $this->flash('success', 'Login successful.');
            Audit::log($username, 'auth.login', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
            $this->redirect('/admin/dashboard');
            return;
        }

        // Failed attempt
        $throttle['attempts']++;
        $backoff = min(self::LOGIN_BACKOFF_MAX, $throttle['attempts']);

        if ($throttle['attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
            $throttle['locked_until'] = time() + self::LOCKOUT_SECONDS;
            App::log('Admin account locked due to failed login attempts', 'warning');
            Audit::log($username ?: 'unknown', 'auth.locked', ['attempts' => $throttle['attempts']]);
        } else {
            App::log('Failed admin login attempt for username: ' . $username, 'info');
            Audit::log($username ?: 'unknown', 'auth.failed', ['attempts' => $throttle['attempts']]);
        }

        Session::set('login_throttle', $throttle);

        // Small delay to slow down brute force (exponential-ish)
        sleep((int) $backoff);

        // preserve username only (not password)
        Session::set('_old', ['username' => $username]);
        $this->flash('error', 'Invalid credentials.');
        $this->redirect('/admin/login');
    }

    public function logout(): void
    {
        $actor = Session::get('user') ?? 'unknown';
        Audit::log($actor, 'auth.logout', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
        Auth::logout();
        Session::destroy();
        $this->flash('success', 'Logged out successfully.');
        $this->redirect('/admin/login');
    }

    /* =====================================================
     * Dashboard
     * ===================================================== */

    public function dashboard(): void
    {
        $this->posts->clearCache();

        $pagesModel = $this->model(\Models\PagesModel::class);
        $mediaModel = $this->model(\Models\MediaModel::class);
        $backupModel = $this->model(\Models\BackupModel::class);

        $pagesCount = count($pagesModel->getAll());
        $mediaCount = count($mediaModel->all());
        $backupsCount = count($backupModel->list());
        $drafts = $this->posts->drafts();
        $recentDrafts = array_slice($drafts, 0, 5, true);

        // Recent Audit Log
        $recentAudit = [];
        $auditPath = App::path('logs', 'audit.log');
        if (file_exists($auditPath)) {
            $lines = file($auditPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (is_array($lines)) {
                $recentLines = array_slice(array_reverse($lines), 0, 6);
                foreach ($recentLines as $line) {
                    $json = json_decode($line, true);
                    if (is_array($json)) {
                        $recentAudit[] = $json;
                    }
                }
            }
        }

        // System Health
        $systemHealthy = is_writable(App::path('content')) && is_writable(App::path('logs'));

        $this->view('admin/dashboard', [
            'posts'         => $this->posts->all(),
            'postsCount'    => $this->posts->publishedCount(),
            'draftCount'    => $this->posts->draftsCount(),
            'trashCount'    => $this->posts->trashedCount(),
            'pagesCount'    => $pagesCount,
            'mediaCount'    => $mediaCount,
            'backupsCount'  => $backupsCount,
            'recentDrafts'  => $recentDrafts,
            'recentAudit'   => $recentAudit,
            'systemHealthy' => $systemHealthy,
            'settings'      => $this->settings->all(),
            'flash'         => $this->getFlash(),
            'csrf_token'    => Session::csrfToken(),
        ]);
    }

    /* =====================================================
     * Posts
     * ===================================================== */

    public function index(): void
    {
        $this->posts->clearCache();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $query = trim((string) ($_GET['q'] ?? ''));
        $statusFilter = trim((string) ($_GET['status'] ?? ''));

        if ($query !== '') {
            $result = $this->posts->search($query, $page, $perPage, false);
        } elseif ($statusFilter === 'draft') {
            $drafts = $this->posts->drafts();
            $total = count($drafts);
            $totalPages = max(1, (int) ceil($total / $perPage));
            $page = max(1, min($page, $totalPages));
            $offset = ($page - 1) * $perPage;
            $items = array_slice($drafts, $offset, $perPage, true);
            $result = [
                'items' => $items,
                'total' => $total,
                'currentPage' => $page,
                'totalPages' => $totalPages,
            ];
        } else {
            $result = $this->posts->publishedPaginated($page, $perPage);
        }

        $publishedCount = $this->posts->publishedCount();
        $draftsCount = $this->posts->draftsCount();
        $trashedCount = $this->posts->trashedCount();
        $totalCount = $publishedCount + $draftsCount + $trashedCount;

        $this->view('admin/posts', [
            'posts'               => $result['items'],
            'currentPage'         => $result['currentPage'],
            'totalPages'          => $result['totalPages'],
            'basePath'            => '/admin',
            'flash'               => $this->getFlash(),
            'csrf_token'          => Session::csrfToken(),
            'searchQuery'         => $query,
            'statusFilter'        => $statusFilter,
            'debugAllCount'       => $totalCount,
            'debugPublishedCount' => $publishedCount,
            'draftsCount'         => $draftsCount,
        ]);
    }

    public function add(): void
    {
        if (!$this->isPost()) {
            $this->view('admin/post-add', [
                'flash' => $this->getFlash(),
                'csrf_token' => Session::csrfToken(),
                'old' => Session::get('_old', []),
            ]);
            return;
        }

        $this->validateCsrf();

        [$title, $content] = $this->validatePost();
        $status = $this->input('status', 'published') === 'draft' ? 'draft' : 'published';
        $customSlug = trim((string) $this->input('slug', ''));
        $excerpt = trim((string) $this->input('excerpt', ''));
        $tagsInput = trim((string) $this->input('tags', ''));
        $tags = array_values(array_filter(array_map('trim', explode(',', $tagsInput))));

        $id = $this->posts->nextId();
        if ($customSlug !== '') {
            $slugCandidate = strtolower(trim(preg_replace('/[^a-z0-9-]+/i', '-', $customSlug), '-'));
            $slug = $slugCandidate !== '' ? $this->posts->generateSlug($slugCandidate) : $this->posts->generateSlug($title);
        } else {
            $slug = $this->posts->generateSlug($title);
        }

        $this->posts->savePost($id, [
            'id'         => $id,
            'title'      => $title,
            'slug'       => $slug,
            'excerpt'    => $excerpt,
            'tags'       => $tags,
            'content'    => $content,
            'status'     => $status,
            'created_at' => time(),
            'updated_at' => time(),
            'deleted_at' => null,
        ]);

        // Audit
        Audit::log(Session::get('user') ?? 'system', 'post.create', ['id' => $id, 'title' => $title, 'status' => $status]);

        $this->flash('success', 'Post created successfully.');
        $this->redirect('/admin');
    }

    public function edit(?int $id): void
    {
        if (!$id || !$post = $this->posts->find($id)) {
            $this->flash('error', 'Post not found.');
            $this->redirect('/admin');
        }

        if ($this->isPost()) {
            $this->validateCsrf();

            [$title, $content] = $this->validatePost();
            $status = $this->input('status', $post['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
            $customSlug = trim((string) $this->input('slug', ''));
            $excerpt = trim((string) $this->input('excerpt', ''));
            $tagsInput = trim((string) $this->input('tags', ''));
            $tags = array_values(array_filter(array_map('trim', explode(',', $tagsInput))));

            $post['title']      = $title;
            $post['content']    = $content;
            $post['status']     = $status;
            $post['excerpt']    = $excerpt;
            $post['tags']       = $tags;

            // Handle slug: if user specified a new custom slug, generate/validate it; otherwise keep existing slug
            if ($customSlug !== '') {
                $slugCandidate = strtolower(trim(preg_replace('/[^a-z0-9-]+/i', '-', $customSlug), '-'));
                if ($slugCandidate !== '' && $slugCandidate !== ($post['slug'] ?? '')) {
                    $post['slug'] = $this->posts->generateSlug($slugCandidate, $id);
                }
            } elseif (empty($post['slug'])) {
                $post['slug'] = $this->posts->generateSlug($title, $id);
            }

            $post['updated_at'] = time();

            $this->posts->savePost($id, $post);

            Audit::log(Session::get('user') ?? 'system', 'post.update', ['id' => $id, 'title' => $title, 'status' => $status]);

            $this->flash('success', 'Post updated successfully.');
            $this->redirect('/admin');
        }

        $this->view('admin/post-edit', [
            'id'    => $id,
            'post'  => $post,
            'flash' => $this->getFlash(),
            'csrf_token' => Session::csrfToken(),
            'old' => Session::get('_old', []),
        ]);
    }

    public function delete(?int $id): void
    {
        if (!$this->isPost()) {
            $this->flash('error', 'Method not allowed. Use POST.');
            $this->redirect('/admin');
        }

        $this->validateCsrf();

        if ($post = $this->posts->find($id)) {
            $post['deleted_at'] = time();
            $this->posts->savePost($id, $post);
            Audit::log(Session::get('user') ?? 'system', 'post.delete', ['id' => $id]);
            $this->flash('success', 'Post moved to trash.');
        } else {
            $this->flash('error', 'Post not found.');
        }

        $this->redirect('/admin');
    }

    public function trash(): void
    {
        $this->view('admin/trash', [
            'posts' => $this->posts->trashed(),
            'flash' => $this->getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function restore(?int $id): void
    {
        if (!$this->isPost()) {
            $this->flash('error', 'Method not allowed. Use POST.');
            $this->redirect('/admin/trash');
        }

        $this->validateCsrf();

        if ($post = $this->posts->find($id)) {
            $post['deleted_at'] = null;
            $this->posts->savePost($id, $post);
            Audit::log(Session::get('user') ?? 'system', 'post.restore', ['id' => $id]);
            $this->flash('success', 'Post restored successfully.');
        } else {
            $this->flash('error', 'Post not found.');
        }

        $this->redirect('/admin/trash');
    }

    public function destroy(?int $id): void
    {
        if (!$this->isPost()) {
            $this->flash('error', 'Method not allowed. Use POST.');
            $this->redirect('/admin/trash');
        }

        $this->validateCsrf();

        $this->posts->deletePost($id);
        Audit::log(Session::get('user') ?? 'system', 'post.destroy', ['id' => $id]);
        $this->flash('success', 'Post permanently deleted.');
        $this->redirect('/admin/trash');
    }

    public function emptyTrash(): void
    {
        if (!$this->isPost()) {
            $this->flash('error', 'Method not allowed. Use POST.');
            $this->redirect('/admin/trash');
        }

        $this->validateCsrf();

        $trashed = $this->posts->trashed();
        $count = count($trashed);

        foreach ($trashed as $id => $_) {
            $this->posts->deletePost($id);
            Audit::log(Session::get('user') ?? 'system', 'post.destroy', ['id' => $id]);
        }

        Audit::log(Session::get('user') ?? 'system', 'post.empty_trash', ['count' => $count]);

        $this->flash('success', "{$count} posts permanently deleted.");
        $this->redirect('/admin/trash');
    }

    /* =====================================================
     * Settings
     * ===================================================== */

    public function settings(): void
    {
        if ($this->isPost()) {
            $this->validateCsrf();

            $data = $_POST;
            unset($data['csrf_token']);

            $settings = [];
            foreach ($data as $key => $value) {
                if (is_scalar($value)) {
                    $settings[(string) $key] = htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
                }
            }

            $this->settings->setMany($settings);
            Session::remove('_old');

            Audit::log(Session::get('user') ?? 'system', 'settings.update', ['keys' => array_keys($settings)]);

            $this->flash('success', 'Settings saved successfully.');
            $this->redirect('/admin/settings');
        }

        $this->view('admin/settings', [
            'settings'   => $this->settings->all(),
            'admin_user' => Session::get('user', 'admin'),
            'flash'      => $this->getFlash(),
            'csrf_token' => Session::csrfToken(),
            'old'        => Session::get('_old', []),
        ]);
    }

    public function security(): void
    {
        if ($this->isPost()) {
            $this->validateCsrf();

            $currentPass = (string) $this->input('current_password', '');
            $newUsername = trim((string) $this->input('username', ''));
            $newPassword = (string) $this->input('new_password', '');
            $confirmPassword = (string) $this->input('confirm_password', '');

            $adminPath = App::path('content', '.admin.php');
            if (!file_exists($adminPath)) {
                $this->flash('error', 'Administrator configuration file not found.');
                $this->redirect('/admin/settings');
            }

            $adminCfg = require $adminPath;
            $savedUser = is_array($adminCfg) ? (string) ($adminCfg['user'] ?? '') : '';
            $savedHash = is_array($adminCfg) ? (string) ($adminCfg['pass'] ?? '') : '';

            if (!password_verify($currentPass, $savedHash)) {
                $this->flash('error', 'Incorrect current password. Verification failed.');
                $this->redirect('/admin/settings');
            }

            if ($newUsername === '') {
                $newUsername = $savedUser;
            }

            if ($newPassword !== '') {
                if (strlen($newPassword) < 8) {
                    $this->flash('error', 'New password must be at least 8 characters long.');
                    $this->redirect('/admin/settings');
                }
                if ($newPassword !== $confirmPassword) {
                    $this->flash('error', 'New password and confirmation do not match.');
                    $this->redirect('/admin/settings');
                }
                $finalHash = password_hash($newPassword, PASSWORD_DEFAULT);
            } else {
                $finalHash = $savedHash;
            }

            $export = "<?php\n\nreturn [\n    'user' => " . var_export($newUsername, true) . ",\n    'pass' => " . var_export($finalHash, true) . ",\n];\n";
            file_put_contents($adminPath, $export, LOCK_EX);

            Session::set('user', $newUsername);
            Audit::log($newUsername, 'auth.credentials_updated', []);

            $this->flash('success', 'Administrator security credentials updated successfully.');
            $this->redirect('/admin/settings');
        }

        $this->redirect('/admin/settings');
    }

    public function setLang(string $code): void
    {
        if (\Core\I18n::isSupported($code)) {
            \Core\I18n::setLocale($code);
            $this->flash('success', __('common.saved'));
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? '/admin';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $refHost = parse_url($referer, PHP_URL_HOST);
        if ($refHost && $refHost !== $host) {
            $referer = '/admin';
        }
        $this->redirect($referer);
    }

    public function bulk(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin');
        }

        $this->validateCsrf();

        $action = (string) $this->input('bulk_action', '');
        $postIds = (array) $this->input('post_ids', []);

        if (empty($postIds) || !in_array($action, ['publish', 'draft', 'trash'], true)) {
            $this->flash('error', __('validation.required', ['field' => 'bulk_action']));
            $this->redirect('/admin');
        }

        $count = 0;
        foreach ($postIds as $id) {
            $id = (int) $id;
            $post = $this->posts->find($id);
            if (!$post) {
                continue;
            }

            if ($action === 'publish') {
                $post['status'] = 'published';
                $post['updated_at'] = time();
                $this->posts->savePost($id, $post);
                $count++;
            } elseif ($action === 'draft') {
                $post['status'] = 'draft';
                $post['updated_at'] = time();
                $this->posts->savePost($id, $post);
                $count++;
            } elseif ($action === 'trash') {
                $post['deleted_at'] = time();
                $this->posts->savePost($id, $post);
                $count++;
            }
        }

        Audit::log(Session::get('user') ?? 'system', 'posts.bulk_' . $action, ['count' => $count]);
        $this->flash('success', __('posts.bulk_success', ['count' => (string) $count]));
        $this->redirect('/admin');
    }

    public function system(): void
    {
        // Directory sizes
        $contentSize = $this->getDirSize(App::path('content'));
        $uploadsSize = $this->getDirSize(App::path('assets', 'uploads'));
        $backupsSize = $this->getDirSize(App::path('backups'));

        // Extension checks
        $requiredExtensions = [
            'json'     => extension_loaded('json'),
            'mbstring' => extension_loaded('mbstring'),
            'fileinfo' => extension_loaded('fileinfo'),
        ];

        $optionalExtensions = [
            'gd'       => extension_loaded('gd'),
            'curl'     => extension_loaded('curl'),
            'zip'      => extension_loaded('zip'),
            'zlib'     => extension_loaded('zlib'),
            'opcache'  => extension_loaded('Zend OPcache'),
        ];

        // Security / Config warnings
        $warnings = [];
        if (ini_get('display_errors') === '1' && !(App::$config['debug'] ?? false)) {
            $warnings[] = 'display_errors is enabled in php.ini. It is recommended to disable it in production.';
        }
        if (!is_writable(App::path('content'))) {
            $warnings[] = 'content/ directory is not writable.';
        }
        if (!is_writable(App::path('logs'))) {
            $warnings[] = 'logs/ directory is not writable.';
        }

        $system = [
            'cms' => [
                'name'    => 'Habrin CMS',
                'version' => App::CORE_VERSION,
                'storage' => 'Flat-file JSON (sharded)',
                'theme'   => App::$config['theme'] ?? 'default',
            ],
            'env' => [
                'php_version' => PHP_VERSION,
                'php_sapi'    => PHP_SAPI,
                'os'          => PHP_OS_FAMILY,
                'timezone'    => date_default_timezone_get(),
                'memory'      => ini_get('memory_limit'),
                'upload_max'  => ini_get('upload_max_filesize'),
                'post_max'    => ini_get('post_max_size'),
                'max_execution_time' => ini_get('max_execution_time'),
            ],
            'paths' => [
                'content_writable' => is_writable(App::path('content')),
                'logs_writable'    => is_writable(App::path('logs')),
                'backups_writable' => is_writable(App::path('backups')),
                'uploads_writable' => is_writable(App::path('assets', 'uploads')),
                'root_writable'    => is_writable(BASE_PATH),
            ],
            'storage' => [
                'content_size' => $this->formatBytes($contentSize),
                'uploads_size' => $this->formatBytes($uploadsSize),
                'backups_size' => $this->formatBytes($backupsSize),
            ],
            'extensions' => [
                'required' => $requiredExtensions,
                'optional' => $optionalExtensions,
            ],
            'warnings' => $warnings,
            'plugins'  => App::$plugins
        ];

        $this->view('admin/system', [
            'system' => $system,
            'flash'  => $this->getFlash(),
        ]);
    }

    /* =====================================================
     * Helpers
     * ===================================================== */

    private function getDirSize(string $path): int
    {
        if (!is_dir($path)) return 0;
        $size = 0;
        try {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
            foreach ($files as $file) {
                $size += $file->getSize();
            }
        } catch (\Throwable $e) {}
        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    private function guard(): void
    {
        // Allow unauthenticated access only to login, logout, and language switch
        $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
        $segments = $uri === '' ? [] : explode('/', $uri);
        $action = $segments[1] ?? 'index';

        if (!in_array($action, ['login', 'logout', 'lang'], true)) {
            Auth::requireAdmin();
        }
    }

    private function validatePost(): array
    {
        $title   = trim((string) $this->input('title', ''));
        $content = trim((string) $this->input('content', ''));

        if ($title === '' || $content === '') {
            Session::set('_old', ['title' => $title, 'content' => $content]);
            $this->flash('error', 'Title and content are required.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin');
        }

        Session::remove('_old');
        return [htmlspecialchars($title, ENT_QUOTES, 'UTF-8'), $content];
    }

    private function autoCleanTrash(): void
    {
        $limit = time() - (self::TRASH_DAYS * 86400);
        $trashed = $this->posts->trashed();
        foreach ($trashed as $id => $post) {
            if ($post['deleted_at'] < $limit) {
                $this->posts->deletePost($id);
            }
        }
    }

    private function validateCsrf(): void
    {
        $token = (string) $this->input('csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            App::log('CSRF validation failed for admin action', 'warning');
            $this->flash('error', 'Invalid CSRF token. Please try again.');
            $this->redirect('/admin/login');
        }
    }
}
