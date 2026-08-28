<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

/**
 * Modern Attribute-aware Router with Dependency Injection Integration.
 */
class Router
{
    private Container $container;
    private array $routes = [];

    public function __construct(?Container $container = null)
    {
        $this->container = $container ?? Container::getInstance();
    }

    /**
     * Register a GET route.
     */
    public function get(string $path, array|callable $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, array|callable $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * Register a PUT route.
     */
    public function put(string $path, array|callable $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Register a DELETE route.
     */
    public function delete(string $path, array|callable $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Internal route registration helper.
     */
    private function addRoute(string $method, string $path, array|callable $handler): self
    {
        // Normalize path
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        // Convert route parameters (e.g., {id}) to regex pattern
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];

        return $this;
    }

    /**
     * Dispatch the current HTTP request to matching route handler.
     */
    public function dispatch(?Request $request = null): void
    {
        $request = $request ?? $this->container->get(Request::class);
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }

                $this->executeHandler($route['handler'], $params, $request);
                return;
            }
        }

        $this->sendNotFound();
    }

    /**
     * Execute route handler with middleware / authorization check and dependency injection.
     */
    private function executeHandler(array|callable $handler, array $params, Request $request): void
    {
        if (is_array($handler)) {
            [$controllerClass, $actionMethod] = $handler;

            $reflector = new ReflectionClass($controllerClass);
            $allowedRoles = [];

            // Read controller-level Authorize attributes
            $classAttributes = $reflector->getAttributes(Authorize::class);
            foreach ($classAttributes as $attribute) {
                $allowedRoles = array_merge($allowedRoles, $attribute->newInstance()->roles);
            }

            // Read method-level Authorize attributes
            if ($reflector->hasMethod($actionMethod)) {
                $methodReflector = $reflector->getMethod($actionMethod);
                $methodAttributes = $methodReflector->getAttributes(Authorize::class);
                foreach ($methodAttributes as $attribute) {
                    $allowedRoles = array_merge($allowedRoles, $attribute->newInstance()->roles);
                }
            }

            // Perform role authorization check
            if (!empty($allowedRoles)) {
                $currentRole = Security::getCurrentRole();
                if (!in_array($currentRole, $allowedRoles, true)) {
                    $this->handleUnauthorized();
                    return;
                }
            }

            // Resolve controller instance via DI container
            $controller = $this->container->get($controllerClass);

            // Execute action with auto-wired parameters + route params
            $result = $this->container->call($controller, $actionMethod, $params);

            if ($result instanceof Response) {
                $result->send();
            }
        } elseif (is_callable($handler)) {
            $result = call_user_func_array($handler, array_values($params));
            if ($result instanceof Response) {
                $result->send();
            }
        }
    }

    /**
     * Handle unauthorized access.
     */
    private function handleUnauthorized(): void
    {
        $isJson = (
            (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '/admin/materials/upload'))
        );

        if ($isJson) {
            if (ob_get_length()) {
                @ob_clean();
            }
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Akses ditolak atau sesi Anda telah berakhir. Silakan login kembali.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $currentRole = Security::getCurrentRole();
        if ($currentRole === Role::GUEST) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        http_response_code(403);
        $title = '403 - Akses Ditolak';
        $viewFile = APP_ROOT . '/Views/errors/403.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<h1>403 Forbidden</h1>";
        }
        exit;
    }

    /**
     * Handle 404 route not found.
     */
    private function sendNotFound(): void
    {
        $isJson = (
            (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '/admin/materials/upload'))
        );

        if ($isJson) {
            if (ob_get_length()) {
                @ob_clean();
            }
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Endpoint tidak ditemukan.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(404);
        $title = '404 - Halaman Tidak Ditemukan';
        $viewFile = APP_ROOT . '/Views/errors/404.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<h1>404 Not Found</h1>";
        }
        exit;
    }
}
