## 开发指南

欢迎贡献 LeafPHPx！本指南帮助您理解项目结构、设置开发环境、编码规范和测试流程。

### 项目结构

```
LeafPHPx/
├── app/                  # 应用代码
│   ├── Controllers/      # 控制器
│   ├── Models/           # 模型
│   └── Middleware/       # 自定义中间件
├── bin/                  # CLI 入口 (leaf)
├── config/               # 配置目录
│   ├── app.php
│   ├── database.php
│   ├── cache.php
│   └── logging.php
├── public/               # Web 入口
│   ├── index.php
│   └── .htaccess
├── resources/            # 资源
│   └── views/            # 视图模板
├── src/                  # 框架核心
│   ├── Leaf/             # LeafPHPx 命名空间
│   │   ├── App.php       # 应用容器
│   │   ├── Config.php    # 配置加载
│   │   ├── Request.php   # PSR-7 请求
│   │   ├── Response.php  # PSR-7 响应
│   │   ├── Database/     # 数据库模块
│   │   ├── Cache/        # 缓存模块
│   │   ├── Validation/   # 验证模块
│   │   ├── View/         # 视图引擎
│   │   ├── Middleware/   # 中间件栈
│   │   ├── Exceptions/   # 异常处理
│   │   ├── Logging/      # 日志
│   │   ├── Console/      # CLI 内核
│   │   └── Attributes/   # 路由属性
│   ├── bootstrap.php     # 引导文件
│   └── helpers.php       # 全局助手函数
├── storage/              # 存储 (logs, cache)
├── tests/                # 测试
├── composer.json         # 依赖
├── .env.example          # 环境示例
└── README.md             # 项目文档
```

### 开发环境设置

1. **克隆并安装**：
   ```bash
   git clone https://github.com/JaneDevStudio/LeafPHPx.git
   cd LeafPHPx
   composer install
   ```

2. **环境配置**：
   - 复制 `.env.example` 到 `.env`，配置数据库等。
   - 确保 PHP >= 8.0，MySQL/Redis（可选）。

3. **启动开发服务器**：
   ```bash
   php bin/leaf serve
   ```

4. **调试**：
   - 设置 `APP_DEBUG=true` 以显示堆栈跟踪。
   - 查看 `storage/logs/app.log` 日志。
   - 在 `bootstrap.php` 中有调试日志输出（error_log）。

5. **IDE 支持**：
   - 使用 PhpStorm 或 VS Code，支持 PHP 8 属性。
   - 启用 Xdebug 以调试容器注入等。

### 编码规范

- **PSR-12**：严格遵守 PSR-12 编码风格。
- **类型提示**：所有方法使用类型提示（PHP 8+）。
- **文档**：使用 PHPDoc 注释公共方法。
- **命名**：
  - 类：PascalCase（e.g., `UserController`）。
  - 方法/变量：camelCase（e.g., `getUser`）。
  - 常量：UPPER_SNAKE_CASE。
- **提交规范**：
  - 使用 Conventional Commits：`feat: add new feature`、`fix: bug fix`、`docs: update README`。
  - 保持提交原子性。

### 测试

- **单元测试**：使用 PHPUnit（`composer require-dev phpunit/phpunit`）。
- **运行测试**：
  ```bash
  ./vendor/bin/phpunit
  ```
- **编写测试**：
  - 扩展 `tests/TestCase.php`。
  - 示例：`tests/Unit/ExampleTest.php`。
  - 测试覆盖核心：路由、容器、数据库等。
- **覆盖率**：
  ```bash
  ./vendor/bin/phpunit --coverage-html coverage
  ```

### 贡献流程

1. **Fork 项目** 并克隆您的 Fork。
2. **创建分支**：`git checkout -b feature/new-feature`。
3. **实现功能**：编写代码、测试、文档。
4. **提交**：
   ```bash
   git add .
   git commit -m "feat: add new feature"
   git push origin feature/new-feature
   ```
5. **Pull Request**：
   - 描述变更、相关 Issue。
   - 确保 CI 通过（GitHub Actions）。
   - 等待审查。

### 常见问题

- **容器注入失败**：检查 `App.php` 中的 `set()` 调用，确保工厂返回正确实例。
- **路由未加载**：确认控制器在 `app/Controllers/`，使用 `#[Route]` 属性。
- **数据库连接错误**：查看 `bootstrap.php` 日志，检查 `.env` 和 `config/database.php`。
- **视图未找到**：路径基于 `resources/views/`，使用点记法（e.g., `user.profile`）。

### 模块扩展

- **新驱动**：在 `Cache/Drivers/` 或 `Database/` 添加类。
- **CLI 命令**：扩展 `Console/Command.php`，在 `Kernel.php` 注册。
- **中间件**：实现 `MiddlewareInterface`，在 `MiddlewareStack` 添加。

如有疑问，打开 Issue 或加入讨论。

---

*最后更新：2025-10-05*