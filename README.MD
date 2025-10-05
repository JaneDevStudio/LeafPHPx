# LeafPHPx

[![PHP Version](https://img.shields.io/badge/PHP-%3E=8.0-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

LeafPHPx 是一个非轻量级、模块化的 PHP 框架，专为现代 Web 开发设计。它基于 PSR 标准构建，支持快速路由、依赖注入、缓存、数据库和视图引擎等功能。框架简洁高效，适合 API 开发和简单 Web 应用。

## 安装

1. **克隆项目**：
   ```bash
   git clone https://github.com/JaneDevStudio/LeafPHPx.git
   cd LeafPHPx
   ```

2. **安装依赖**：
   ```bash
   composer install
   ```

3. **复制环境文件**：
   ```bash
   cp .env.example .env
   ```

4. **配置数据库**（可选）：
   编辑 `.env` 文件，设置数据库连接：
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **启动开发服务器**：
   ```bash
   php bin/leaf serve
   ```
   访问 `http://localhost:8000`，您将看到欢迎页面。

## 快速开始

### 路由定义

使用属性路由（推荐）：

在 `app/Controllers/ExampleController.php` 中：
```php
<?php

namespace App\Controllers;

use Leaf\Attributes\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExampleController
{
    #[Route('/hello/{name}', methods: ['GET'])]
    public function hello(ServerRequestInterface $request, ResponseInterface $response, string $name): ResponseInterface
    {
        return $response->json(['message' => "Hello, $name!"]);
    }
}
```

框架会自动扫描 `app/Controllers/` 目录加载路由。

手动路由（在 `config/routes.php` 中）：
```php
$router->get('/manual', function() { return 'Manual Route'; });
```

### 数据库模型

创建模型 `app/Models/User.php`：
```php
<?php

namespace App\Models;

use Leaf\Database\Model;

class User extends Model
{
    protected static string $table = 'users';
}
```

使用：
```php
$user = User::find(1);
$users = User::all();
```

### 视图渲染

创建视图 `resources/views/hello.php`：
```php
<h1>Hello, <?= $name ?>!</h1>
```

在控制器中：
```php
return $response->view('hello', ['name' => $name]);
```

### 中间件

创建自定义中间件 `app/Middleware/AuthMiddleware.php`（示例见代码）。

在 `src/bootstrap.php` 中注册：
```php
$app->middlewareStack->add(new \App\Middleware\AuthMiddleware());
```

### 生成代码

使用 CLI 生成控制器：
```bash
php bin/leaf make:controller UserController
```

运行迁移（占位）：
```bash
php bin/leaf migrate
```

## 配置

配置位于 `config/` 目录：

- `app.php`：环境、调试、模块开关。
- `database.php`：数据库连接。
- `cache.php`：缓存驱动。
- `logging.php`：日志路径。

支持 YAML/JSON 配置格式。

## 示例

- **API 响应**：`$response->json(['data' => $data]);`
- **重定向**：`$response->redirect('/home', 302);`
- **验证**：使用 `$validator = app()->validator; $validator->validate($data, $rules);`

完整示例见 `app/Controllers/ExampleController.php`。

## 测试

运行单元测试：
```bash
composer test  # 或 phpunit
```

## 贡献

欢迎贡献！请阅读 [Dev.md](Dev.md) 获取开发指南。

## 许可证

MIT License. 详见 [LICENSE](LICENSE) 文件。

---

# 致谢

感谢 [nikic/fast-route](https://github.com/nikic/FastRoute)、[Monolog](https://github.com/Seldaek/monolog) 等开源项目。

[GitHub 仓库](https://github.com/JaneDevStudio/LeafPHPx) | [文档](docs/)