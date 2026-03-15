<?php declare(strict_types=1);

namespace App\Route;

use App\Core\Response;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $callback): void
    {
        $this->routes[] = [
            'method'   => strtoupper($method),
            'pattern'  => $pattern,
            'callback' => $callback
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            $pattern = "@^" . $route['pattern'] . "$@";

            if ($route['method'] === strtoupper($method) && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($route['callback'], $matches);
                return;
            }
        }

        Response::error('Route not found');
    }
}

