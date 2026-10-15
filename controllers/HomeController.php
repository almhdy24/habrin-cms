<?php
/**
 * Pico JSON CMS — Home Controller
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

        $result = $this->posts->published($page, $perPage);

        // Calculate range
        $total = $result['total'];
        $currentPage = $result['currentPage'];
        $totalPages = $result['totalPages'];
        $items = $result['items'];

        $start = ($currentPage - 1) * $perPage + 1;
        $end = min($start + $perPage - 1, $total);

        $this->view('pages/home', [
            'posts'       => $items,
            'currentPage' => $currentPage,
            'totalPages'  => $totalPages,
            'totalPosts'  => $total,
            'start'       => $start,
            'end'         => $end,
            'settings'    => $this->settings->all(),
        ]);
    }

    public function viewPost(string $slug): void
    {
        $post = $this->posts->findBySlug($slug);

        if (!$post) {
            http_response_code(404);
            $this->view('pages/404', ['message' => 'Post not found']);
            return;
        }

        $this->view('pages/single', [
            'post'     => $post,
            'settings' => $this->settings->all(),
        ]);
    }
}
