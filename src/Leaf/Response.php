<?php

namespace Leaf;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    private string $protocol = '1.1';
    private int $status = 200;
    private string $reason = '';
    private array $headers = [];
    private StreamInterface $body;

    private const PHRASES = [
        200 => 'OK', 201 => 'Created', 204 => 'No Content',
        301 => 'Moved Permanently', 302 => 'Found', 304 => 'Not Modified',
        400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden',
        404 => 'Not Found', 405 => 'Method Not Allowed', 500 => 'Internal Server Error',
    ];

    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', string $reason = '')
    {
        $this->status = $status;
        $this->headers = array_change_key_case($headers, CASE_LOWER);
        $this->body = $body instanceof StreamInterface ? $body : new Stream(fopen('php://temp', 'r+'));
        $this->protocol = $version;
        $this->reason = $reason ?: self::PHRASES[$status] ?? '';
    }

    /* ---------- 常用助手 ---------- */
    public function json($data, int $options = JSON_THROW_ON_ERROR): self
    {
        $this->body->write(json_encode($data, $options));
        return $this->withHeader('Content-Type', 'application/json');
    }

    public function redirect(string $url, int $status = 302): self
    {
        return $this->withStatus($status)->withHeader('Location', $url);
    }

    public function view(string $template, array $data = []): self
    {
        $html = (new View\ViewEngine('php'))->render($template, $data);
        $this->body->write($html);
        return $this->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /* ---------- MessageInterface ---------- */
    public function getProtocolVersion(): string        { return $this->protocol; }
    public function withProtocolVersion($version): self { $new = clone $this; $new->protocol = $version; return $new; }

    public function getHeaders(): array                 { return $this->headers; }
    public function hasHeader($name): bool              { return isset($this->headers[strtolower($name)]); }
    public function getHeader($name): array             { return $this->hasHeader($name) ? $this->headers[strtolower($name)] : []; }
    public function getHeaderLine($name): string        { return implode(', ', $this->getHeader($name)); }
    public function withHeader($name, $value): self
    { $new = clone $this; $new->headers[strtolower($name)] = (array) $value; return $new; }
    public function withAddedHeader($name, $value): self
    { $new = clone $this; $new->headers[strtolower($name)][] = $value; return $new; }
    public function withoutHeader($name): self
    { $new = clone $this; unset($new->headers[strtolower($name)]); return $new; }

    public function getBody(): StreamInterface          { return $this->body; }
    public function withBody(StreamInterface $body): self { $new = clone $this; $new->body = $body; return $new; }

    /* ---------- ResponseInterface ---------- */
    public function getStatusCode(): int                { return $this->status; }
    public function withStatus($code, $reasonPhrase = ''): self
    { $new = clone $this; $new->status = (int) $code; $new->reason = $reasonPhrase ?: self::PHRASES[$code] ?? ''; return $new; }
    public function getReasonPhrase(): string           { return $this->reason; }
}
