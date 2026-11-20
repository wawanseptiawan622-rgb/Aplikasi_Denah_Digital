<?php
/**
 * Controller dasar: menyediakan render view dengan layout & respon JSON.
 */
class Controller
{
    protected array $config;

    public function __construct()
    {
        $this->config = require BASE_PATH . '/config/config.php';
    }

    public function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $data['config']  = $this->config;
        $data['baseUrl'] = base_url();
        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . "/app/views/{$view}.php";
        $content = ob_get_clean();

        require BASE_PATH . "/app/views/layouts/{$layout}.php";
    }

    protected function json(mixed $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
