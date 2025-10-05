<?php

namespace Leaf\View;

class ViewEngine
{
    protected $engine;

    public function __construct(string $type)
    {
        $this->engine = match ($type) {
            'php' => new Engines\PhpEngine(),
            'twig' => new Engines\TwigEngine(),
            default => throw new \Exception('Invalid view engine'),
        };
    }

    public function render(string $view, array $data = []): string
    {
        return $this->engine->render($view, $data);
    }
}