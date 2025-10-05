<?php

namespace Leaf\Database;

class QueryBuilder
{
    protected Medoo $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    // Chainable methods: where, select, etc.
    // Wrapper around Medoo
}