<?php

namespace Leaf\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareStack implements RequestHandlerInterface
{
    protected array $middlewares = [];

    public function add(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function process(ServerRequestInterface $request, callable $handler): ResponseInterface
    {
        $stack = array_reverse($this->middlewares);
        $next = $handler;

        foreach ($stack as $middleware) {
            $next = fn($req) => $middleware->process($req, new class($next) implements RequestHandlerInterface {
                private $next;
                public function __construct(callable $next) { $this->next = $next; }
                public function handle(ServerRequestInterface $request): ResponseInterface { return ($this->next)($request); }
            });
        }

        return $next($request);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw new \RuntimeException('No handler');
    }
}