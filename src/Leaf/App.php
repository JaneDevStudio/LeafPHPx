<?php

namespace Leaf;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Leaf\Exceptions\ExceptionHandler;
use Leaf\Logging\Logger;
use Leaf\Middleware\MiddlewareStack;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App implements ContainerInterface
{
    /** 全局单例 */
    protected static ?self $instance = null;

    protected Container $container;
    protected MiddlewareStack $middlewareStack;
    protected ExceptionHandler $exceptionHandler;

    /** 统一路由表（属性 + 手动） */
    protected array $routeTable = [];

    public function __construct()
    {
        self::$instance = $this;   // 自动绑定
        $this->container       = new Container();
        $this->middlewareStack = new MiddlewareStack();
        $this->exceptionHandler = new ExceptionHandler();

        // 核心服务注册
        $this->container->set(ServerRequestInterface::class, fn() => Request::fromGlobals());
        $this->container->set(ResponseInterface::class, fn() => new Response());
        $this->container->set(Logger::class, fn() => new Logger());
        $this->container->set(Config::class, fn() => new Config());
        $this->container->set(\Leaf\Console\Kernel::class, fn() => new \Leaf\Console\Kernel());
    }

    /* ---------- 单例入口 ---------- */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /* ---------- 容器接口 ---------- */
    public function get(string $id)        { return $this->container->get($id); }
    public function has(string $id): bool  { return $this->container->has($id); }

    /* ---------- 魔法访问器（app()->db） ---------- */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /* ---------- 可选模块注册 ---------- */
    public function registerDatabase(): void   { $this->container->set('db', fn() => Database\Connection::getInstance()); }
    public function registerCache(): void      { $this->container->set('cache', fn() => new Cache\Cache()); }
    public function registerValidation(): void { $this->container->set('validator', fn() => new Validation\Validator()); }
    public function registerView(string $engine): void { $this->container->set('view', fn() => new View\ViewEngine($engine)); }

    /* ---------- 路由注册（手动 + 属性共用） ---------- */
    public function addRoute(array $methods, string $path, $handler): void
    {
        $this->routeTable[] = ['methods' => $methods, 'path' => $path, 'handler' => $handler];
    }

    /* ---------- 路由快捷方法（避免与容器 get 冲突） ---------- */
    public function addGet(string $path, callable|string $handler): self
    {
        $this->addRoute(['GET'], $path, $handler);
        return $this;
    }

    public function addPost(string $path, callable|string $handler): self
    {
        $this->addRoute(['POST'], $path, $handler);
        return $this;
    }

    public function addPut(string $path, callable|string $handler): self
    {
        $this->addRoute(['PUT'], $path, $handler);
        return $this;
    }

    public function addDelete(string $path, callable|string $handler): self
    {
        $this->addRoute(['DELETE'], $path, $handler);
        return $this;
    }

    /* ---------- 属性路由扫描 ---------- */
    public function loadRoutesFromControllers(string $dir): void
    {
        foreach (glob($dir . '/*.php') as $file) {
            $class = 'App\\Controllers\\' . basename($file, '.php');
            $refl  = new \ReflectionClass($class);
            foreach ($refl->getMethods() as $method) {
                foreach ($method->getAttributes(Attributes\Route::class) as $attr) {
                    $route = $attr->newInstance();
                    $this->addRoute($route->methods, $route->path, [$class, $method->getName()]);
                }
            }
        }
    }

    /* ---------- 运行入口 ---------- */
    public function run(): void
    {
        try {
            $request  = $this->get(ServerRequestInterface::class);
            $response = $this->handle($request);
            $this->emit($response);
        } catch (\Throwable $e) {
            $response = $this->exceptionHandler->handle($e, $this->get(ResponseInterface::class));
            $this->emit($response);
        }
    }

    /* ---------- 请求处理 ---------- */
    protected function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routeTable as $route) {
                $r->addRoute($route['methods'], $route['path'], $route['handler']);
            }
        });

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new Exceptions\HttpException(404);
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new Exceptions\HttpException(405);
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars    = $routeInfo[2];
                return $this->middlewareStack->process($request, function ($req) use ($handler, $vars) {
                    if (is_callable($handler)) {
                        return call_user_func_array($handler, array_merge([$req, $this->get(ResponseInterface::class)], $vars));
                    }
                    if (is_array($handler) && count($handler) === 2) {
                        $controller = $this->container->resolve($handler[0]);
                        return call_user_func_array([$controller, $handler[1]], array_merge([$req, $this->get(ResponseInterface::class)], $vars));
                    }
                    throw new \RuntimeException('Invalid handler');
                });
        }
    }

    /* ---------- 响应发送 ---------- */
    protected function emit(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
        echo $response->getBody();
    }
}