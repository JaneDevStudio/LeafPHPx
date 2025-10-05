<?php

namespace Leaf\Cache\Drivers;

use Psr\SimpleCache\CacheInterface;
use Redis;

class RedisDriver implements CacheInterface
{
    protected Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(\Leaf\Config::get('cache.redis.host'), \Leaf\Config::get('cache.redis.port'));
    }

    public function get(string $key, mixed $default = null): mixed { $val = $this->redis->get($key); return $val === false ? $default : unserialize($val); }
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool { return $this->redis->set($key, serialize($value), $ttl ?? 0); }
    // ...
}