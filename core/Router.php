<?php
/**
 * Router sederhana: mendaftarkan rute "Controller@method".
 */
class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Dukung penempatan di subfolder (mis. Apache /denah-digital/public)
        $base = base_path();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = $this->normalize($path);

        $handler = $this->routes[$method][$path] ?? null;
        if ($handler === null) {
            http_response_code(404);
            (new Controller())->render('404', ['title' => 'Halaman tidak ditemukan']);
            return;
        }

        [$class, $action] = explode('@', $handler);
        (new $class())->$action();
    }

    private function normalize(string $path): string
    {
        return '/' . trim($path, '/');
    }
}
