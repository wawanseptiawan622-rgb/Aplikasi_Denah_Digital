<?php
/**
 * Bootstrap bersama: dipakai oleh public/index.php (lokal/Apache)
 * dan api/index.php (Vercel).
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Autoloader sederhana
spl_autoload_register(function (string $class): void {
    foreach (['core', 'app/controllers', 'app/models'] as $dir) {
        $file = BASE_PATH . "/{$dir}/{$class}.php";
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

/**
 * Path dasar aplikasi (otomatis).
 * - php -S ... -t public            -> ""
 * - Laragon virtual host (.test)    -> ""
 * - Laragon http://localhost/denah-digital/ (via .htaccess root) -> "/denah-digital"
 */
function base_path(): string
{
    // Vercel: api/index.php mendefinisikan APP_BASE_PATH = '' (aplikasi ada di root domain)
    if (defined('APP_BASE_PATH')) {
        return APP_BASE_PATH;
    }
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if (str_ends_with($dir, '/public') && !str_starts_with($uri, $dir)) {
        $dir = substr($dir, 0, -7);
    }
    return $dir;
}

/** URL dasar aplikasi (dipakai untuk isi QR Code). Bisa dipaksa lewat config 'base_url'. */
function base_url(): string
{
    $cfg = require BASE_PATH . '/config/config.php';
    if (!empty($cfg['base_url'])) {
        return rtrim($cfg['base_url'], '/') . '/';
    }
    $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    $scheme = $https ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return "{$scheme}://{$host}" . base_path() . '/';
}

/** Helper escape HTML */
function e(?string $v): string
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// Daftar rute
$router = new Router();
$router->get('/',               'HomeController@index');
$router->get('/api/facilities', 'HomeController@facilities');

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
// Di Vercel, pastikan akses langsung ke /api/index.php dianggap halaman utama
if (defined('APP_BASE_PATH') && (parse_url($requestUri, PHP_URL_PATH) === '/api/index.php')) {
    $requestUri = '/';
}

$router->dispatch($requestUri, $_SERVER['REQUEST_METHOD']);
