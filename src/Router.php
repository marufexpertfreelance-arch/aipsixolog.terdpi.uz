<?php
declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // нормализуем хвостовые слэши: /hemis/callback/ -> /hemis/callback
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        $handler = $this->routes[$method][$path] ?? null;
        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        // Если обработчик задан как [ClassName::class, 'method'],
        // создаём экземпляр класса автоматически.
        if (is_array($handler) && is_string($handler[0])) {
            $className = $handler[0];
            $methodName = $handler[1];
            $instance = new $className();
            $handler = [$instance, $methodName];
        }

        call_user_func($handler);
    }
}
