<?php

namespace Leaf\Cache\Drivers;

use Psr\SimpleCache\CacheInterface;

class ApcuDriver implements CacheInterface
{
    // Use APCu functions
    public function get(string $key, mixed $default = null): mixed { return apcu_fetch($key, $success) ? apcu_fetch($key) : $default; }
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool { return apcu_store($key, $value, $ttl ?? 0); }
    // ...
}