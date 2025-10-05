<?php

namespace Leaf\Exceptions;

class HttpException extends \RuntimeException
{
    public function __construct(int $code = 500, string $message = '')
    {
        parent::__construct($message ?: $this->defaultMessage($code), $code);
    }

    private function defaultMessage(int $code): string
    {
        return match ($code) {
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            500 => 'Internal Server Error',
            default => 'Unknown Error',
        };
    }
}
