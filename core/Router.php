<?php
declare(strict_types=1);
namespace Core;
use Core\View, Core\App, Core\Container, Core\Hooks;

final class Router
{
    private array $routes = [];
    private static bool $registered = false;

    public function __construct()
    {
        if (!self::$registered) {
            // Register plugin routes via hooks
            Hooks::do_action('register_routes', $this);

            // Built-in health endpoint route (protected by HealthController)
            $this->addRoute('GET', '/_health', ['Controllers\\HealthController', 'index']);

            self::$registered = true;
        }
    }

    public function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[$method][] = ['path' => $path, 'handler' => $handler];
    }

    public function dispatch(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Plugin routes
        $route = $this->matchRoute($method, $path);
        if ($route !== null) {
            [$controllerClass, $methodName] = $route['handler'];
            $params = array_values($route['params']);
            $this->dispatchController($controllerClass, $methodName, $params);
            return;
        }

        $uri = trim($path ?? '', '/');
        $segments = $uri === '' ? [] : explode('/', $uri);

        // Admin: Theme activate
        if (($segments[0] ?? '') === 'admin' && ($segments[1] ?? '') === 'themes' && ($segments[2] ?? '') === 'activate' && isset($segments[3])) {
            $this->dispatchController('Controllers\\AdminThemesController', 'activate', [$segments[3]]);
            return;
        }

        // Admin: Plugin settings
        if (($segments[0] ?? '') === 'admin' && ($segments[1] ?? '') === 'plugins' && ($segments[2] ?? '') === 'settings' && isset($segments[3])) {
            $this->dispatchController('Controllers\\AdminPluginsController', 'settings', [$segments[3]]);
            return;
        }

        // Admin: Static pages & menu
        if (($segments[0] ?? '') === 'admin' && isset($segments[1]) && in_array($segments[1], ['pages', 'menu', 'plugins', 'themes'])) {
            $controllerClass = 'Controllers\\Admin' . ucfirst($segments[1]) . 'Controller';
            $methodName = $segments[2] ?? 'index';
            $params = array_slice($segments, 3);
            $this->dispatchController($controllerClass, $methodName, $params);
            return;
        }

        // Static Pages
        $pageSlug = $segments[0] ?? null;
        if ($pageSlug && !in_array($pageSlug, ['admin', 'post', 'installer', 'assets', 'themes'])) {
            try {
                $pagesModel = new \Models\PagesModel();
                if ($pagesModel->findBySlug($pageSlug)) {
                    $this->dispatchController('Controllers\\PageController', 'show', [$pageSlug]);
                    return;
                }
            } catch (\Throwable $e) {}
        }

        // Default Routing
        $controllerName = $segments[0] ?? 'home';
        $methodName = $segments[1] ?? 'index';
        $params = array_slice($segments, 2);

        if ($controllerName === 'post' && isset($segments[1])) {
            $controllerClass = 'Controllers\\HomeController';
            $methodName = 'viewPost';
            $params = [$segments[1]];
        } else {
            $controllerClass = 'Controllers\\' . ucfirst($controllerName) . 'Controller';
        }

        $this->dispatchController($controllerClass, $methodName, $params);
    }

    private function matchRoute(string $method, string $path): ?array
    {
        if (empty($this->routes[$method])) return null;
        foreach ($this->routes[$method] as $route) {
            $pattern = preg_quote($route['path'], '#');
            $pattern = preg_replace('#\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\}#', '(?P<$1>[^/]+)', $pattern);
            if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) $params[$key] = $value;
                }
                return ['handler' => $route['handler'], 'params' => $params];
            }
        }
        return null;
    }

    private function dispatchController(string $controllerClass, string $method, array $params): void
    {
        Hooks::do_action('beforeController', $controllerClass, $method, $params);
        if (!class_exists($controllerClass)) $this->show404('Controller not found');
        $controller = Container::has($controllerClass) ? Container::get($controllerClass) : new $controllerClass();
        if (!is_callable([$controller, $method])) $this->show404('Method not found');
        call_user_func_array([$controller, $method], $params);
        Hooks::do_action('afterController', $controllerClass, $method, $params);
    }

    private function show404(string $message = 'Page not found'): void
    {
        http_response_code(404);
        View::render('pages/404', ['message' => $message]);
        exit;
    }
}
