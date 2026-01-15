<?php
namespace App\Core;

class Router
{
    private array $routes = ['GET' => [], 'POST' => []];
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        $base = rtrim(parse_url($this->config['app']['base_url'], PHP_URL_PATH) ?? '', '/');
        if ($base && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        if ($uri === '') {
            $uri = '/';
        }

        [$handler, $params] = $this->match($method, $uri);

        if (!$handler) {
            http_response_code(404);
            echo 'Not Found';
            return;
        }

        [$controllerName, $action] = explode('@', $handler);
        $fqcn = 'App\\Controllers\\' . $controllerName;
        if (!class_exists($fqcn)) {
            http_response_code(500);
            echo 'Controller not found: ' . htmlspecialchars($fqcn);
            return;
        }

        $controller = new $fqcn($this->config);
        if (!method_exists($controller, $action)) {
            http_response_code(500);
            echo 'Action not found: ' . htmlspecialchars($action);
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }

    private function match(string $method, string $uri): array
    {
        $routes = $this->routes[$method] ?? [];
        foreach ($routes as $path => $handler) {
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $path);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                return [$handler, $matches];
            }
        }
        return [null, []];
    }
}