<?php
namespace App\Core;

class View
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function render(string $view, array $data = []): void
    {
        $baseUrl = rtrim($this->config['app']['base_url'], '/');
        $title   = $data['title'] ?? 'JunkTracker';
        $user    = $data['user']  ?? null;
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: $viewFile");
        }
        extract($data);
        include __DIR__ . '/../Views/layouts/main.php';
    }
}