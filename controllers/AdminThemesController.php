<?php
/**
 * Admin - Theme Management
 */

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Session;
use Core\Auth;
use Core\App;
use Models\ThemesModel;

class AdminThemesController extends Controller
{
    private ThemesModel $themes;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
        $this->themes = new ThemesModel();
    }

    public function index(): void
    {
        $themes = $this->themes->getAll();
        $active = $this->themes->getActive();
        $errors = $this->themes->getErrors();
        $activeData = $this->themes->getActiveData();

        $this->view('admin/themes/index', [
            'themes' => $themes,
            'activeTheme' => $active,
            'activeData' => $activeData,
            'errors' => $errors,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function activate(string $theme): void
    {
        if ($this->isPost()) {
            $this->validateCsrf();

            $this->themes = new ThemesModel(); // refresh
            if (!$this->themes->isValid($theme)) {
                Session::flash('error', "Theme '{$theme}' not found.");
                $this->redirect('/admin/themes');
                return;
            }

            if ($this->themes->activate($theme)) {
                Session::flash('success', "Theme '{$theme}' activated successfully.");
            } else {
                $errors = $this->themes->getErrors();
                $msg = $errors['activate'] ?? "Failed to activate theme '{$theme}'.";
                Session::flash('error', $msg);
            }
            $this->redirect('/admin/themes');
        }

        // Show confirmation (GET)
        $themes = $this->themes->getAll();
        if (!isset($themes[$theme])) {
            Session::flash('error', 'Theme not found.');
            $this->redirect('/admin/themes');
        }

        $this->view('admin/themes/activate', [
            'theme' => $theme,
            'themeData' => $themes[$theme],
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    private function validateCsrf(): void
    {
        $token = $this->input('csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/admin/themes');
        }
    }
}
