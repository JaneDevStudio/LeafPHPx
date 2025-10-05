<?php

namespace Leaf\Database;

use Medoo\Medoo;

class Connection
{
    protected static ?Medoo $instance = null;

    public static function getInstance(): Medoo
{
    error_log('=== CONNECTION DEBUG ===');
    $default = \Leaf\Config::get('database.default') ?? 'mysql';
    error_log('Resolved default: ' . $default);

    // 手动多级获取，绕过点记法 bug
    $dbConfig = \Leaf\Config::get('database');
    $config = $dbConfig['connections'][$default] ?? [];

    error_log('Manual config: ' . print_r($config, true));
    if (empty($config)) {
        throw new \Exception('DB config empty for ' . $default . '. Check connections in database.php');
    }
    if (!is_array($config)) {
        throw new \Exception('DB config not array: ' . gettype($config));
    }

    self::$instance = new Medoo($config);
    error_log('Medoo instance created successfully.');
    error_log('=== END CONNECTION ===');
    return self::$instance;
}
}

