<?php
/**
 * Static Pages Controller
 * Handles /about, /contact, etc.
 */

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\PagesModel;
use Models\SettingsModel;

class PageController extends Controller
{
    private PagesModel $pages;
    private SettingsModel $settings;

    public function __construct()
    {
        parent::__construct();
        $this->pages = $this->model(PagesModel::class);
        $this->settings = $this->model(SettingsModel::class);
    }

    public function show(string $slug): void
    {
        $page = $this->pages->findBySlug($slug);
        if (!$page) {
            http_response_code(404);
            $this->view('pages/404', ['message' => 'Page not found']);
            return;
        }

        $this->view('pages/page', [
            'page' => $page,
            'settings' => $this->settings->all(),
        ]);
    }
}
