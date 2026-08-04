<?php

namespace Controllers;

use Core\Controller;
use Core\Session;
use Core\Auth;
use Core\App;
use Core\PluginLoader;
use Core\PluginSettings;

class AdminPluginsController extends Controller
{
    private array $pluginStatus;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
        $this->loadPluginStatus();
        PluginSettings::init();
    }

    private function loadPluginStatus(): void
    {
        $path = App::path('content', 'plugins.json');
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            $this->pluginStatus = is_array($data) ? $data : [];
        } else {
            $this->pluginStatus = [];
        }
    }

    private function savePluginStatus(): void
    {
        file_put_contents(
            App::path('content', 'plugins.json'),
            json_encode($this->pluginStatus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    private function readManifest(string $name): array
    {
        $dir = App::path('plugins', $name);
        $file = $dir . '/plugin.json';
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    public function index(): void
    {
        $loader = new PluginLoader(App::path('plugins'));
        $available = $loader->discover();

        $plugins = [];
        foreach ($available as $name => $info) {
            $manifest = $this->readManifest($name);
            $plugins[$name] = [
                'name' => $name,
                'file' => $info['file'],
                'enabled' => $this->pluginStatus[$name] ?? true,
                'description' => $manifest['description'] ?? '',
                'version' => $manifest['version'] ?? '1.0.0',
                'has_settings' => PluginSettings::hasForm($name),
            ];
        }

        $this->view('admin/plugins/index', [
            'plugins' => $plugins,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function toggle(string $name): void
    {
        if ($this->isPost()) {
            $this->validateCsrf();
            $current = $this->pluginStatus[$name] ?? true;
            $this->pluginStatus[$name] = !$current;
            $this->savePluginStatus();

            Session::flash('success', "Plugin '{$name}' " . ($this->pluginStatus[$name] ? 'enabled' : 'disabled'));
            $this->redirect('/admin/plugins');
        }

        $loader = new PluginLoader(App::path('plugins'));
        $available = $loader->discover();
        if (!isset($available[$name])) {
            Session::flash('error', 'Plugin not found.');
            $this->redirect('/admin/plugins');
        }

        $this->view('admin/plugins/toggle', [
            'plugin' => $name,
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function settings(string $name): void
    {
        $loader = new PluginLoader(App::path('plugins'));
        $available = $loader->discover();
        if (!isset($available[$name])) {
            Session::flash('error', 'Plugin not found.');
            $this->redirect('/admin/plugins');
        }

        if (!PluginSettings::hasForm($name)) {
            Session::flash('error', 'This plugin has no settings.');
            $this->redirect('/admin/plugins');
        }

        if ($this->isPost()) {
            $this->validateCsrf();
            $input = $_POST;
            unset($input['csrf_token']);

            $result = PluginSettings::save($name, $input);

            if ($result['valid']) {
                Session::flash('success', "Settings saved for '{$name}'.");
                $this->redirect('/admin/plugins/settings/' . $name);
            } else {
                // Show errors and repopulate form with submitted data
                $errors = $result['errors'];
                $flashErrors = [];
                foreach ($errors as $field => $msg) {
                    $flashErrors[] = $msg;
                }
                Session::flash('error', implode(' ', $flashErrors));

                $this->view('admin/plugins/settings', [
                    'plugin' => $name,
                    'settings' => $result['data'],
                    'errors' => $errors,
                    'flash' => Session::getFlash(),
                    'csrf_token' => Session::csrfToken(),
                ]);
                return;
            }
        }

        $current = PluginSettings::getEffective($name);
        $this->view('admin/plugins/settings', [
            'plugin' => $name,
            'settings' => $current,
            'errors' => [],
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    private function validateCsrf(): void
    {
        $token = $this->input('csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/admin/plugins');
        }
    }
}
