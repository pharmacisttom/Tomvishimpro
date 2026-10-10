<?php

namespace Tomvis\HIMPro\Core\Routing;

use Tomvis\HIMPro\Core\Container\Container;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Closure;
use Exception;

class Router
{
    protected Container $container;
    protected array $routes = [];
    protected array $groupStack = [];
    protected array $middlewareMap = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function registerMiddleware(string $name, string $class): void
    {
        $this->middlewareMap[$name] = $class;
    }

    public function addRoute(string $method, string $uri, mixed $action): Route
    {
        $prefix = '';
        $groupMiddleware = [];

        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $groupMiddleware = array_merge($groupMiddleware, (array)$group['middleware']);
            }
        }

        $fullUri = $prefix . '/' . trim($uri, '/');
        $fullUri = '/' . trim($fullUri, '/');

        $route = new Route($method, $fullUri, $action);
        if (!empty($groupMiddleware)) {
            $route->middleware($groupMiddleware);
        }

        $this->routes[] = $route;
        return $route;
    }

    public function get(string $uri, mixed $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, mixed $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, mixed $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, mixed $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, mixed $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function group(array $attributes, Closure $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        $matchingMethodExists = false;

        foreach ($this->routes as $route) {
            if ($route->matches($method, $uri)) {
                return $this->runRoute($route, $request);
            }

            if ($route->getUri() === $uri && $route->getMethod() !== $method) {
                $matchingMethodExists = true;
            }
        }

        if ($matchingMethodExists) {
            if ($request->isJson()) {
                return JsonResponse::error('Method Not Allowed', 405);
            }
            return new Response('405 Method Not Allowed', 405);
        }

        if ($request->isJson()) {
            return JsonResponse::error('Route Not Found', 404);
        }

        return new Response('404 Not Found', 404);
    }

    protected function runRoute(Route $route, Request $request): Response
    {
        $middlewares = $route->getMiddleware();
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middlewareName) {
                return function ($request) use ($next, $middlewareName) {
                    $middlewareClass = $this->middlewareMap[$middlewareName] ?? $middlewareName;
                    if (!class_exists($middlewareClass)) {
                        throw new Exception("Middleware [{$middlewareName}] not found.");
                    }
                    $instance = $this->container->make($middlewareClass);
                    return $instance->handle($request, $next);
                };
            },
            function ($request) use ($route) {
                return $this->runAction($route->getAction(), $request, $route->getParameters());
            }
        );

        return $pipeline($request);
    }

    protected function runAction(mixed $action, Request $request, array $parameters): Response
    {
        if ($action instanceof Closure) {
            $result = call_user_func_array($action, array_merge([$request], array_values($parameters)));
        } elseif (is_array($action)) {
            list($controllerClass, $method) = $action;
            $controller = $this->container->make($controllerClass);
            $result = call_user_func_array([$controller, $method], array_merge([$request], array_values($parameters)));
        } elseif (is_string($action) && str_contains($action, '@')) {
            list($controllerClass, $method) = explode('@', $action);
            $controller = $this->container->make($controllerClass);
            $result = call_user_func_array([$controller, $method], array_merge([$request], array_values($parameters)));
        } else {
            throw new Exception("Invalid route action provided.");
        }

        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            return new JsonResponse($result);
        }

        return new Response((string)$result);
    }
}
