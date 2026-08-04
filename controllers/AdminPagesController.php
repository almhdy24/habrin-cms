<?php
/**
 * Admin - Pages Management
 */

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Session;
use Core\Auth;
use Models\PagesModel;

class AdminPagesController extends Controller
{
    private PagesModel $pages;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
        $this->pages = $this->model(PagesModel::class);
    }

    public function index(): void
    {
        $pages = $this->pages->getAll();
        $this->view('admin/pages/index', [
            'pages' => $pages,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $this->validateCsrf();
            $slug = trim($this->input('slug'));
            $title = trim($this->input('title'));
            $content = trim($this->input('content'));

            if (empty($slug) || empty($title) || empty($content)) {
                Session::flash('error', 'All fields are required.');
                $this->redirect('/admin/pages/create');
            }

            // Slugify
            $slug = strtolower(trim(preg_replace('/[^a-z0-9-]+/', '-', $slug), '-'));
            if (empty($slug)) $slug = 'page';

            if ($this->pages->findBySlug($slug)) {
                Session::flash('error', 'Page with this slug already exists.');
                $this->redirect('/admin/pages/create');
            }

            $this->pages->savePage($slug, [
                'title' => htmlspecialchars($title),
                'content' => $content,
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            Session::flash('success', 'Page created successfully.');
            $this->redirect('/admin/pages');
        }

        $this->view('admin/pages/form', [
            'page' => null,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function edit(string $slug): void
    {
        $page = $this->pages->findBySlug($slug);
        if (!$page) {
            Session::flash('error', 'Page not found.');
            $this->redirect('/admin/pages');
        }

        if ($this->isPost()) {
            $this->validateCsrf();
            $title = trim($this->input('title'));
            $content = trim($this->input('content'));

            if (empty($title) || empty($content)) {
                Session::flash('error', 'All fields are required.');
                $this->redirect('/admin/pages/edit/' . $slug);
            }

            $page['title'] = htmlspecialchars($title);
            $page['content'] = $content;
            $page['updated_at'] = time();

            $this->pages->savePage($slug, $page);

            Session::flash('success', 'Page updated successfully.');
            $this->redirect('/admin/pages');
        }

        $this->view('admin/pages/form', [
            'page' => $page,
            'slug' => $slug,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function delete(string $slug): void
    {
        $page = $this->pages->findBySlug($slug);
        if (!$page) {
            Session::flash('error', 'Page not found.');
            $this->redirect('/admin/pages');
        }

        if ($this->isPost()) {
            $this->validateCsrf();
            $this->pages->deletePage($slug);
            Session::flash('success', 'Page deleted successfully.');
            $this->redirect('/admin/pages');
        }

        $this->view('admin/pages/delete', [
            'page' => $page,
            'slug' => $slug,
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    private function validateCsrf(): void
    {
        $token = $this->input('csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/admin/pages');
        }
    }
}
