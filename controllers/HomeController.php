<?php
/**
 * Habrin CMS — Home Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\App;
use Core\View;
use Models\PostsModel;
use Models\SettingsModel;

final class HomeController extends Controller
{
    private PostsModel $posts;
    private SettingsModel $settings;

    public function __construct()
    {
        parent::__construct();

        // Check for theme preview
        $this->handleThemePreview();

        $this->posts = $this->model(PostsModel::class);
        $this->settings = $this->model(SettingsModel::class);
    }

    /**
     * Handle theme preview via URL parameter (restricted to authenticated admin)
     */
    private function handleThemePreview(): void
    {
        if (isset($_GET['theme_preview'])) {
            if (!\Core\Auth::check()) {
                return;
            }

            $theme = trim((string) $_GET['theme_preview']);
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $theme)) {
                return;
            }

            $themesModel = new \Models\ThemesModel();
            if ($themesModel->isValid($theme)) {
                // Temporarily override for this request
                App::$config['theme'] = $theme;
                // Clear view cache to reload templates
                View::clearCache();
                // Log preview
                App::log("Theme preview by admin: {$theme}", 'info');
            }
        }
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 5;
        $searchQuery = trim((string) ($_GET['q'] ?? ''));

        if ($searchQuery !== '') {
            $result = $this->posts->search($searchQuery, $page, $perPage, true);
        } else {
            $result = $this->posts->published($page, $perPage);
        }

        // Calculate range
        $total = $result['total'];
        $currentPage = $result['currentPage'];
        $totalPages = $result['totalPages'];
        $items = $result['items'];

        $start = $total > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
        $end = min($start + $perPage - 1, $total);

        $this->view('pages/home', [
            'posts'       => $items,
            'currentPage' => $currentPage,
            'totalPages'  => $totalPages,
            'totalPosts'  => $total,
            'start'       => $start,
            'end'         => $end,
            'searchQuery' => $searchQuery,
            'settings'    => $this->settings->all(),
        ]);
    }

    public function viewPost(string $slug): void
    {
        $isAdmin = \Core\Auth::check();
        $post = $this->posts->findBySlug($slug, $isAdmin);

        if (!$post) {
            http_response_code(404);
            $this->view('pages/404', ['message' => 'Post not found']);
            return;
        }

        View::share(['current_post' => $post]);

        $this->view('pages/single', [
            'post'     => $post,
            'isDraft'  => ($post['status'] ?? 'published') === 'draft',
            'settings' => $this->settings->all(),
        ]);
    }
}
