<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Session;
use Core\Auth;
use Core\Audit;
use Models\MediaModel;

final class AdminMediaController extends Controller
{
    private MediaModel $media;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
        $this->media = new MediaModel();
    }

    public function index(): void
    {
        $items = $this->media->getAll();

        $this->view('admin/media/index', [
            'items' => $items,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function json(): void
    {
        $items = $this->media->getAll();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'items' => $items,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function upload(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/media');
        }

        $this->validateCsrf();

        if (empty($_FILES['file'])) {
            $this->flash('error', 'Please select a file to upload.');
            $this->redirect('/admin/media');
        }

        $result = $this->media->upload($_FILES['file']);
        if ($result['success']) {
            Audit::log(Session::get('user') ?? 'system', 'media.upload', ['file' => $result['filename']]);
            $this->flash('success', 'File uploaded successfully.');
        } else {
            $this->flash('error', $result['error'] ?? 'Upload failed.');
        }

        $this->redirect('/admin/media');
    }

    public function delete(string $filename): void
    {
        if (!$this->isPost()) {
            $this->flash('error', 'Method not allowed. Use POST.');
            $this->redirect('/admin/media');
        }

        $this->validateCsrf();

        if ($this->media->delete($filename)) {
            Audit::log(Session::get('user') ?? 'system', 'media.delete', ['file' => $filename]);
            $this->flash('success', 'File deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete file.');
        }

        $this->redirect('/admin/media');
    }

    private function validateCsrf(): void
    {
        $token = (string) $this->input('csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            $this->flash('error', 'Invalid CSRF token.');
            $this->redirect('/admin/media');
        }
    }
}
