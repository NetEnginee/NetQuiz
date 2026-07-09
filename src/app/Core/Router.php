<?php
declare(strict_types=1);
namespace App\Core;

class Router
{
    protected $routes = [];

    /**
     * Add a route with URL pattern matching and named parameters.
     */
    public function add($method, $path, $handler)
    {
        // Convert paths like /quiz/{id} to regex like ^/quiz/(?P<id>[a-zA-Z0-9_-]+)$
        $routePattern = preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $path);
        $routePattern = '#^' . $routePattern . '$#';

        $this->routes[$method][$routePattern] = $handler;
    }

    public function get($path, $handler)
    {
        $this->add('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * Dispatch the current request to the matched controller action or callback.
     */
    public function dispatch($url, $method)
    {
        // Strip query string from URL
        $url = parse_url($url, PHP_URL_PATH);
        $url = rtrim($url, '/') ?: '/';
        $method = strtoupper($method);

        if (!isset($this->routes[$method])) {
            $this->sendNotFound();
            return;
        }

        foreach ($this->routes[$method] as $pattern => $handler) {
            if (preg_match($pattern, $url, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, function ($key) {
                    return !is_int($key);
                }, ARRAY_FILTER_USE_KEY);

                if (is_array($handler)) {
                    $controllerName = $handler[0];
                    $actionName = $handler[1];

                    if (class_exists($controllerName)) {
                        // 1. Resolve Authorization via Attributes (PHP 8 Feature)
                        $reflector = new \ReflectionClass($controllerName);
                        $allowedRoles = [];

                        // Read controller-level Authorize attributes
                        $classAttributes = $reflector->getAttributes(Authorize::class);
                        foreach ($classAttributes as $attribute) {
                            $allowedRoles = array_merge($allowedRoles, $attribute->newInstance()->roles);
                        }

                        // Read action-level Authorize attributes
                        if ($reflector->hasMethod($actionName)) {
                            $methodReflector = $reflector->getMethod($actionName);
                            $methodAttributes = $methodReflector->getAttributes(Authorize::class);
                            foreach ($methodAttributes as $attribute) {
                                $allowedRoles = array_merge($allowedRoles, $attribute->newInstance()->roles);
                            }
                        }

                        // Perform authorization check if roles are specified
                        if (!empty($allowedRoles)) {
                            $currentRole = Security::getCurrentRole();
                            if (!in_array($currentRole, $allowedRoles, true)) {
                                $this->handleUnauthorized();
                                return;
                            }
                        }

                        $controller = new $controllerName();
                        if (method_exists($controller, $actionName)) {
                            call_user_func_array([$controller, $actionName], $params);
                            return;
                        }
                    }
                } elseif (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                    return;
                }
            }
        }

        $this->sendNotFound();
    }

    /**
     * Handle unauthorized access.
     */
    protected function handleUnauthorized(): void
    {
        $currentRole = Security::getCurrentRole();
        if ($currentRole === Role::GUEST) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        http_response_code(403);
        $title = '403 - Akses Ditolak';
        require_once APP_ROOT . '/Views/errors/403.php';
        exit;
    }

    protected function sendNotFound()
    {
        http_response_code(404);
        $title = '404 - Halaman Tidak Ditemukan';
        require_once APP_ROOT . '/Views/errors/404.php';
        exit;
    }
}
