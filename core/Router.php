<?php

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $method = strtoupper($method);
        $path = $this->normalizePath($path);
        $this->routes[$method][$path] = $handler;
    }

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function match(array $methods, string $path, callable $handler): void
    {
        foreach ($methods as $method) {
            $this->add($method, $path, $handler);
        }
    }

    public function dispatch(string $requestMethod, string $requestUri, string $basePath = ''): void
    {
        $method = strtoupper($requestMethod);
        $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        if ($basePath !== '' && $basePath !== '/' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        $path = $this->normalizePath($path);
        if ($path === '/index.php') {
            $path = '/';
        }
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            $this->notFound();
            return;
        }

        $handler();
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    private function notFound(): void
    {
        http_response_code(404);
        require __DIR__ . '/../404.php';
    }
}
