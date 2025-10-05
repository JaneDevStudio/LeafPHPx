<?php

namespace Leaf;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    private string $scheme = '';
    private string $host = '';
    private ?int $port = null;
    private string $path = '';
    private string $query = '';
    private string $fragment = '';
    private string $userInfo = '';

    public function __construct(string $uri = '')
    {
        $parts = parse_url($uri) ?: [];
        $this->scheme   = $parts['scheme']   ?? '';
        $this->host     = $parts['host']     ?? '';
        $this->port     = $parts['port']     ?? null;
        $this->path     = $parts['path']     ?? '';
        $this->query    = $parts['query']    ?? '';
        $this->fragment = $parts['fragment'] ?? '';
        $this->userInfo = ($parts['user'] ?? '') . ($parts['pass'] ?? '' ? ':' . $parts['pass'] : '');
    }

    public function getScheme(): string        { return $this->scheme; }
    public function getAuthority(): string
    {
        if ($this->host === '') return '';
        $auth = $this->userInfo;
        if ($auth !== '') $auth .= '@';
        $auth .= $this->host;
        if ($this->port !== null) $auth .= ':' . $this->port;
        return $auth;
    }
    public function getUserInfo(): string      { return $this->userInfo; }
    public function getHost(): string          { return $this->host; }
    public function getPort(): ?int            { return $this->port; }
    public function getPath(): string          { return $this->path; }
    public function getQuery(): string         { return $this->query; }
    public function getFragment(): string      { return $this->fragment; }

    public function withScheme($scheme): self   { $new = clone $this; $new->scheme = $scheme; return $new; }
    public function withUserInfo($user, $password = null): self
    { $new = clone $this; $new->userInfo = $user . ($password ? ':' . $password : ''); return $new; }
    public function withHost($host): self       { $new = clone $this; $new->host = $host; return $new; }
    public function withPort($port): self       { $new = clone $this; $new->port = $port; return $new; }
    public function withPath($path): self       { $new = clone $this; $new->path = $path; return $new; }
    public function withQuery($query): self     { $new = clone $this; $new->query = $query; return $new; }
    public function withFragment($fragment): self { $new = clone $this; $new->fragment = $fragment; return $new; }

    public function __toString(): string
    {
        $uri = '';
        if ($this->scheme !== '') $uri .= $this->scheme . ':';
        if ($this->getAuthority() !== '') $uri .= '//' . $this->getAuthority();
        $uri .= $this->path;
        if ($this->query !== '') $uri .= '?' . $this->query;
        if ($this->fragment !== '') $uri .= '#' . $this->fragment;
        return $uri;
    }
}