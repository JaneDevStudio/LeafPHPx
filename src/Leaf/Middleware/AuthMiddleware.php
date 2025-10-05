<?php

namespace Leaf\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Example: Check auth
        if (!$request->getHeader('Authorization')) {
            return (new \Leaf\Response())->withStatus(401)->json(['error' => 'Unauthorized']);
        }
        return $handler->handle($request);
    }
}