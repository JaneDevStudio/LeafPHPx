<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Base test class with setup for app
    protected function setUp(): void
    {
        parent::setUp();
        // Bootstrap app for tests
    }
}