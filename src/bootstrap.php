<?php

use Leaf\App;
use Leaf\Config;

require __DIR__ . '/../vendor/autoload.php';

/* ---------- 载入环境变量 ---------- */
// Load .env
if (file_exists(__DIR__ . '/../.env')) {
    foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = array_map('trim', explode('=', $line, 2));
            putenv("{$key}={$value}");
        }
    }
}
error_log('=== ENV DEBUG ===');
error_log('DB_CONNECTION: ' . var_export(getenv('DB_CONNECTION'), true));
error_log('DB_HOST: ' . var_export(getenv('DB_HOST'), true));
error_log('DB_PASSWORD: ' . var_export(getenv('DB_PASSWORD'), true));
// 检查 .env 文件是否存在/可读
$envPath = __DIR__ . '/../.env';
error_log('ENV file exists: ' . (file_exists($envPath) ? 'YES' : 'NO'));
if (file_exists($envPath)) {
    error_log('ENV content: ' . file_get_contents($envPath));
}
error_log('=== END ENV ===');
/* ---------- 实例化应用 ---------- */
$app = new App();
Config::load(__DIR__ . '/../config/');

/* ---------- 根据配置注册模块 ---------- */
$config = Config::get('app');
if ($config['modules']['database'] ?? false) {
    $app->registerDatabase();
}
if ($config['modules']['cache'] ?? false) {
    $app->registerCache();
}
if ($config['modules']['validation'] ?? false) {
    $app->registerValidation();
}
if ($viewEngine = $config['modules']['view'] ?? false) {
    $app->registerView($viewEngine);
}
error_log('=== CONFIG DEBUG ===');
// 直接获取具体键，避免无参调用
$dbConfig = \Leaf\Config::get('database');  // 获取整个 'database' 配置
error_log('database config: ' . (is_array($dbConfig) ? print_r($dbConfig, true) : 'NULL or ' . gettype($dbConfig)));

$default = \Leaf\Config::get('database.default', 'mysql');  // 用默认值防 null
error_log('DB default: ' . var_export($default, true));

$connConfig = \Leaf\Config::get('database.connections.mysql', []);  // 用空数组默认
error_log('MySQL conn config: ' . (is_array($connConfig) ? print_r($connConfig, true) : 'NULL or ' . gettype($connConfig)));


error_log('=== END CONFIG ===');
/* ---------- 加载路由 ---------- */
$app->loadRoutesFromControllers(__DIR__ . '/../app/Controllers/');
require __DIR__ . '/../config/routes.php';

return $app;
