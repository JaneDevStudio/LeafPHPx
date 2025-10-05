<?php

namespace Leaf\View\Engines;

class PhpEngine
{
    protected string $basePath;

    public function __construct(string $basePath = '')
{
    $this->basePath = $basePath ?: $_SERVER['DOCUMENT_ROOT'] . '/../resources/views/';
}

    public function render(string $view, array $data = []): string
    {
        $file = $this->basePath . str_replace('.', '/', $view) . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View file not found: $file");
        }
        extract($data);
        ob_start();
        include $file;
        return ob_get_clean();
    }
}
