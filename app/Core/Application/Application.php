<?php

namespace Tomvis\HIMPro\Core\Application;

use Tomvis\HIMPro\Core\Container\Container;
use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Config\DotEnv;
use Tomvis\HIMPro\Core\Routing\Router;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use Tomvis\HIMPro\Core\Logging\Logger;
use Tomvis\HIMPro\Core\Exceptions\ExceptionHandler;
use Tomvis\HIMPro\Core\Events\EventDispatcher;

class Application
{
    protected static ?Application $instance = null;
    protected Container $container;
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
        self::$instance = $this;

        $this->container = Container::getInstance();
        $this->container->instance(Application::class, $this);
        $this->container->instance(Container::class, $this->container);

        $this->bootstrap();
    }

    public static function getInstance(): ?Application
    {
        return self::$instance;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    protected function bootstrap(): void
    {
        // 1. Load Environment
        DotEnv::load($this->basePath . '/.env');

        // 2. Load Configs
        Config::loadFromDir($this->basePath . '/config');

        // 3. Register Core Services
        $logger = new Logger($this->basePath . '/storage/logs/app.log');
        $this->container->instance(Logger::class, $logger);

        $exceptionHandler = new ExceptionHandler($logger);
        $exceptionHandler->register();
        $this->container->instance(ExceptionHandler::class, $exceptionHandler);

        $dbManager = new DatabaseManager();
        $this->container->instance(DatabaseManager::class, $dbManager);

        $router = new Router($this->container);
        $this->container->instance(Router::class, $router);

        $eventDispatcher = new EventDispatcher();
        $this->container->instance(EventDispatcher::class, $eventDispatcher);

        // 4. Register Standard Middlewares
        $router->registerMiddleware('auth', \Tomvis\HIMPro\Core\Middleware\AuthMiddleware::class);
        $router->registerMiddleware('rbac', \Tomvis\HIMPro\Core\Middleware\RbacMiddleware::class);
        $router->registerMiddleware('csrf', \Tomvis\HIMPro\Core\Middleware\CsrfMiddleware::class);
        $router->registerMiddleware('rate_limit', \Tomvis\HIMPro\Core\Middleware\RateLimitMiddleware::class);
    }
}
