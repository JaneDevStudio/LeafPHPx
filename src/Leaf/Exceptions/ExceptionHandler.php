<?php

namespace Leaf\Exceptions;

use Leaf\Config;
use Psr\Http\Message\ResponseInterface;

class ExceptionHandler
{
    public function handle(\Throwable $e, ResponseInterface $response): ResponseInterface
    {
        $code = method_exists($e, 'getCode') && $e->getCode() ? $e->getCode() : 500;
        $data = [
            'error'   => $e::class,
            'message' => $e->getMessage() ?: 'Unknown error',   // 空消息兜底
            'file'    => Config::get('app.debug', false) ? $e->getFile() : null,
            'line'    => Config::get('app.debug', false) ? $e->getLine() : null,
        ];

        if (Config::get('app.debug', false)) {
            $data['trace'] = $e->getTraceAsString();
        }

        return $response
            ->withStatus($code)
            ->withHeader('Content-Type', 'application/json')
            ->json($data);
    }
}
