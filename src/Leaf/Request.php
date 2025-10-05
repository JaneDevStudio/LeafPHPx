<?php

namespace Leaf;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

class Request implements ServerRequestInterface
{
    private string $method;
    private string $requestTarget;
    private UriInterface $uri;
    private string $protocol = '1.1';
    private array $headers = [];
    private StreamInterface $body;
    private array $serverParams;
    private array $cookieParams = [];
    private array $queryParams = [];
    private array $uploadedFiles = [];
    private $parsedBody = null;
    private array $attributes = [];

    public function __construct(string $method = 'GET', $uri = '', array $headers = [], $body = null, string $version = '1.1', array $serverParams = [])
    {
        $this->method = strtoupper($method);
        $this->uri = $uri instanceof UriInterface ? $uri : new Uri($uri);

        // Force header values to array (PSR-7 requirement)
        $this->headers = [];
        foreach ($headers as $name => $value) {
            $this->headers[strtolower($name)] = (array) $value;
        }

        $this->body = $body instanceof StreamInterface ? $body : new Stream(fopen('php://temp', 'r+'));
        $this->protocol = $version;
        $this->serverParams = $serverParams;
    }

    /* ---------- 快速工厂 ---------- */
    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .
               ($_SERVER['HTTP_HOST'] ?? '') .
               ($_SERVER['REQUEST_URI'] ?? '/');
        $headers = getallheaders() ?: [];

        // Force header values to array
        $normalizedHeaders = [];
        foreach ($headers as $name => $value) {
            $normalizedHeaders[strtolower($name)] = (array) $value;
        }

        $body = new Stream(fopen('php://input', 'r'));
        $request = new self($method, $uri, $normalizedHeaders, $body, '1.1', $_SERVER);

        // 解析查询参数
        $request->queryParams = $_GET;

        // 解析 cookie
        $request->cookieParams = $_COOKIE;

        // 解析上传文件
        $request->uploadedFiles = self::normalizeFiles($_FILES);

        return $request;
    }

    /* ---------- 文件规范化 ---------- */
    private static function normalizeFiles(array $files): array
    {
        $normalized = [];
        foreach ($files as $name => $file) {
            if (is_array($file['tmp_name'])) {
                $normalized[$name] = [];
                foreach ($file['tmp_name'] as $key => $tmpName) {
                    $normalized[$name][] = new UploadedFile(
                        $tmpName,
                        $file['size'][$key] ?? 0,
                        $file['error'][$key] ?? UPLOAD_ERR_NO_FILE,
                        $file['name'][$key] ?? '',
                        $file['type'][$key] ?? ''
                    );
                }
            } else {
                $normalized[$name] = new UploadedFile(
                    $file['tmp_name'],
                    $file['size'],
                    $file['error'],
                    $file['name'],
                    $file['type']
                );
            }
        }
        return $normalized;
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

    /* ---------- RequestInterface ---------- */
    public function getRequestTarget(): string
    { return $this->requestTarget ?: $this->uri->__toString(); }
    public function withRequestTarget($requestTarget): self
    { $new = clone $this; $new->requestTarget = $requestTarget; return $new; }

    public function getMethod(): string   { return $this->method; }
    public function withMethod($method): self
    { $new = clone $this; $new->method = strtoupper($method); return $new; }

    public function getUri(): UriInterface { return $this->uri; }
    public function withUri(UriInterface $uri, $preserveHost = false): self
    { $new = clone $this; $new->uri = $uri; if (!$preserveHost && $uri->getHost()) $new->headers['host'] = [$uri->getHost()]; return $new; }

    /* ---------- ServerRequestInterface ---------- */
    public function getServerParams(): array            { return $this->serverParams; }
    public function getCookieParams(): array            { return $this->cookieParams; }
    public function withCookieParams(array $cookies): self
    { $new = clone $this; $new->cookieParams = $cookies; return $new; }

    public function getQueryParams(): array             { return $this->queryParams; }
    public function withQueryParams(array $query): self
    { $new = clone $this; $new->queryParams = $query; return $new; }

    public function getUploadedFiles(): array           { return $this->uploadedFiles; }
    public function withUploadedFiles(array $uploadedFiles): self
    { $new = clone $this; $new->uploadedFiles = $uploadedFiles; return $new; }

    public function getParsedBody()
    {
        if ($this->parsedBody !== null) {
            return $this->parsedBody;
        }

        if ($this->method === 'GET') {
            return [];
        }

        $contentType = $this->getHeaderLine('Content-Type');
        $bodyContent = $this->getBody()->getContents();

        if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            parse_str($bodyContent, $parsed);
            $this->parsedBody = $parsed;
        } elseif (strpos($contentType, 'application/json') !== false) {
            $this->parsedBody = json_decode($bodyContent, true) ?: [];
        } else {
            // Fallback to $_POST for other types
            $this->parsedBody = $_POST;
        }

        return $this->parsedBody;
    }

    public function withParsedBody($data): self
    { $new = clone $this; $new->parsedBody = $data; return $new; }

    public function getAttributes(): array              { return $this->attributes; }
    public function getAttribute($name, $default = null){ return $this->attributes[$name] ?? $default; }
    public function withAttribute($name, $value): self
    { $new = clone $this; $new->attributes[$name] = $value; return $new; }
    public function withoutAttribute($name): self
    { $new = clone $this; unset($new->attributes[$name]); return $new; }
}