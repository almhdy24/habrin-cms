<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Session;
use Core\Auth;
use Core\Audit;
use Core\App;
use Models\BackupModel;

final class AdminBackupController extends Controller
{
    private BackupModel $backup;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
        $this->backup = new BackupModel();
    }

    public function index(): void
    {
        $backups = $this->backup->getAll();

        $this->view('admin/backup/index', [
            'backups' => $backups,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function create(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/backup');
        }

        $this->validateCsrf();

        $result = $this->backup->create();
        if ($result['success']) {
            Audit::log(Session::get('user') ?? 'system', 'backup.create', ['file' => $result['filename']]);
            $this->flash('success', "Backup '{$result['filename']}' created successfully.");
        } else {
            $this->flash('error', $result['error'] ?? 'Backup creation failed.');
        }

        $this->redirect('/admin/backup');
    }

    public function download(string $filename): void
    {
        $filename = basename($filename);
        $filepath = App::path('backups', $filename);

        if (!is_file($filepath)) {
            $this->flash('error', 'Backup file not found.');
            $this->redirect('/admin/backup');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    public function delete(string $filename): void
    {
        if (!$this->isPost()) {
            $this->flash('error', 'Method not allowed. Use POST.');
            $this->redirect('/admin/backup');
        }

        $this->validateCsrf();

        if ($this->backup->delete($filename)) {
            Audit::log(Session::get('user') ?? 'system', 'backup.delete', ['file' => $filename]);
            $this->flash('success', 'Backup deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete backup file.');
        }

        $this->redirect('/admin/backup');
    }

    public function restore(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/backup');
        }

        $this->validateCsrf();

        if (empty($_FILES['backup_file'])) {
            $this->flash('error', 'Please choose a backup file to restore.');
            $this->redirect('/admin/backup');
        }

        $result = $this->backup->restore($_FILES['backup_file']);
        if ($result['success']) {
            Audit::log(Session::get('user') ?? 'system', 'backup.restore', ['file' => $_FILES['backup_file']['name'] ?? 'uploaded']);
            $this->flash('success', 'Backup restored successfully.');
        } else {
            $this->flash('error', $result['error'] ?? 'Backup restoration failed.');
        }

        $this->redirect('/admin/backup');
    }

    private function validateCsrf(): void
    {
        $token = (string) $this->input('csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            $this->flash('error', 'Invalid CSRF token.');
            $this->redirect('/admin/backup');
        }
    }
}
