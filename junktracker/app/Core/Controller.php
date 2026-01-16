<?php
namespace App\Core;

class Controller
{
    protected array $config;
    protected Auth $auth;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->auth   = new Auth($config);
    }

    protected function render(string $view, array $data = []): void
    {
        $viewObj = new View($this->config);
        $viewObj->render($view, $data);
    }

    protected function redirect(string $path): void
    {
        $base = rtrim($this->config['app']['base_url'], '/');
        header('Location: ' . $base . $path);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!$this->auth->user()) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $user = $this->auth->user();
        if (!$user || (int)$user['role'] < (int)$this->config['app']['admin_role_min']) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}