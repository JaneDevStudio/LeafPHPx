<?php

namespace Leaf\Database;

abstract class Model
{
    protected static string $table;

    public static function find(int $id)
    {
        return Connection::getInstance()->get(static::$table, '*', ['id' => $id]);
    }

    // More methods: all, create, update, delete...
    public static function all()
    {
        return Connection::getInstance()->select(static::$table, '*');
    }

    // ...
}