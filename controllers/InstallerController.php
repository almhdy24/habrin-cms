<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\App;
use Core\Session;

final class InstallerController extends Controller
{
    private string $lockFile;
    private string $settingsFile;
    private string $adminFile;

    public function __construct()
    {
        parent::__construct();

        $this->lockFile     = App::path('content', '.installed');
        $this->settingsFile = App::path('content', 'settings.json');
        $this->adminFile    = App::path('content', '.admin.php');

        if ($this->isInstalled()) {
            $this->redirect('/');
        }

        Session::set(
            'installer.step',
            Session::get('installer.step', 'system')
        );
    }

    /* =====================================================
     * Entry
     * ===================================================== */

    public function index(): void
    {
        $step = Session::get('installer.step', 'system');

        if (!in_array($step, ['system', 'admin', 'finish'], true)) {
            $step = 'system';
            Session::set('installer.step', $step);
        }

        match ($step) {
            'system' => $this->system(),
            'admin'  => $this->admin(),
            'finish' => $this->finish(),
        };
    }

    /* =====================================================
     * Step 1: System Check
     * ===================================================== */

    private function system(): void
    {
        $checks = [
            'php'     => PHP_VERSION_ID >= 80100,
            'json'    => function_exists('json_encode'),
            'content' => is_writable(App::path('content')),
            'root'    => is_writable(BASE_PATH),
        ];

        if ($this->isPost()) {
            if (!in_array(false, $checks, true)) {
                Session::set('installer.step', 'admin');
                $this->redirect('/installer');
            }

            $this->flash('error', 'System requirements not met.');
        }

        $this->view('installer/system', [
            'checks' => $checks,
            'step'   => 'system',
            'flash'  => $this->getFlash(),
        ]);
    }

    /* =====================================================
     * Step 2: Admin Setup
     * ===================================================== */

    private function admin(): void
    {
        if ($this->isPost()) {
            $this->validateCsrf();
            $this->handleAdminInstall();
            return;
        }

        $this->view('installer/admin', [
            'step'  => 'admin',
            'flash' => $this->getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    private function handleAdminInstall(): void
    {
        $site  = trim((string) $this->input('site_name'));
        $user  = trim((string) $this->input('admin_user'));
        $pass  = (string) $this->input('admin_pass');
        $pass2 = (string) $this->input('admin_pass_confirm');

        /* ---------- Validation ---------- */

        if ($site === '' || $user === '' || $pass === '' || $pass2 === '') {
            $this->fail('All fields are required.');
        }

        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $user)) {
            $this->fail('Admin username must be 3–20 characters.');
        }

        if ($pass !== $pass2 || strlen($pass) < 8) {
            $this->fail('Password must be at least 8 characters.');
        }

        /* ---------- Persist ---------- */

        try {
            $settings = [
                'site_title' => htmlspecialchars($site, ENT_QUOTES, 'UTF-8'), // Changed from site_name to site_title
                'site_description' => '',
                'site_keywords' => '',
                'meta' => [
                    'installed_at' => time(),
                    'core_version' => App::CORE_VERSION,
                    'php_version'  => PHP_VERSION,
                ],
            ];

            file_put_contents(
                $this->settingsFile,
                json_encode($settings, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                LOCK_EX
            );

            file_put_contents(
                $this->adminFile,
                "<?php\nreturn " . var_export([
                    'user' => $user,
                    'pass' => password_hash($pass, PASSWORD_DEFAULT),
                ], true) . ";\n",
                LOCK_EX
            );

            chmod($this->adminFile, 0600);

            Session::set('installer.step', 'finish');
            $this->redirect('/installer');

        } catch (\Throwable) {
            @unlink($this->settingsFile);
            @unlink($this->adminFile);
            $this->fail('Installation failed.');
        }
    }

    /* =====================================================
     * Step 3: Finish
     * ===================================================== */

    private function finish(): void
    {
        try {
            file_put_contents(
                $this->lockFile,
                json_encode([
                    'installed_at' => time(),
                    'core_version' => App::CORE_VERSION,
                    'php_version'  => PHP_VERSION,
                ], JSON_PRETTY_PRINT),
                LOCK_EX
            );

            chmod($this->lockFile, 0600);
            Session::remove('installer.step');

            $this->view('installer/finish', [
                'step' => 'finish',
            ]);

        } catch (\Throwable) {
            $this->fail('Finalization failed.');
        }
    }

    /* =====================================================
     * Helpers
     * ===================================================== */

    private function isInstalled(): bool
    {
        return is_file($this->lockFile);
    }

    private function fail(string $message): never
    {
        $this->flash('error', $message);
        $this->redirect('/installer');
        exit;
    }

    private function validateCsrf(): void
    {
        $token = $this->input('csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            $this->fail('Invalid CSRF token.');
        }
    }
}
