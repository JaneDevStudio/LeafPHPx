<?php

use Leaf\App;

if (!function_exists('app')) {
    /**
     * 获取当前 App 容器实例
     * @return App
     */
    function app(): App
    {
        return App::getInstance();
    }
}
