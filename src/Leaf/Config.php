<?php

namespace Leaf;

use Symfony\Component\Yaml\Yaml;

class Config
{
    protected static array $configs = [];

    public static function load(string $dir): void
    {
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            $key = basename($file, '.php');
            if ($key === 'routes') continue;  // 排除 routes.php（非配置，返回脚本）
            self::$configs[$key] = require $file;
        }
        // Support YAML/JSON
        $yamlFiles = glob($dir . '/*.yaml');
        foreach ($yamlFiles as $file) {
            $key = basename($file, '.yaml');
            self::$configs[$key] = Yaml::parseFile($file);
        }
        $jsonFiles = glob($dir . '/*.json');
        foreach ($jsonFiles as $file) {
            $key = basename($file, '.json');
            self::$configs[$key] = json_decode(file_get_contents($file), true);
        }
    }

    public static function get(string $key, $default = null)
    {
        if (empty($key)) {
            return $default;
        }
        $keys = explode('.', $key);
        $value = self::$configs;
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        return $value ?? $default;
    }
}