<?php

namespace Leaf\Cache\Drivers;

use Psr\SimpleCache\CacheInterface;

class FileDriver implements CacheInterface
{
    protected string $path;

    public function __construct()
    {
        $this->path = \Leaf\Config::get('cache.file.path');
        if (!is_dir($this->path)) mkdir($this->path, 0755, true);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->path . md5($key) . '.cache';
        if (!file_exists($file)) return $default;
        $data = unserialize(file_get_contents($file));
        if ($data['ttl'] && time() > $data['ttl']) {
            unlink($file);
            return $default;
        }
        return $data['value'];
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $file = $this->path . md5($key) . '.cache';
        $ttl = $ttl ? time() + ($ttl instanceof \DateInterval ? (new \DateTime())->add($ttl)->getTimestamp() - time() : $ttl) : null;
        return file_put_contents($file, serialize(['value' => $value, 'ttl' => $ttl])) !== false;
    }

    // Implement other methods similarly...
    public function delete(string $key): bool { /* ... */ return true; }
    public function clear(): bool { /* ... */ return true; }
    public function getMultiple(iterable $keys, mixed $default = null): iterable { /* ... */ return []; }
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool { /* ... */ return true; }
    public function deleteMultiple(iterable $keys): bool { /* ... */ return true; }
    public function has(string $key): bool { /* ... */ return true; }
}