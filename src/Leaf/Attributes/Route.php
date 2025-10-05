<?php

namespace Leaf\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(public string $path, public array $methods = ['GET']) {}
}