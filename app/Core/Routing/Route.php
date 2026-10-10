<?php

namespace Tomvis\HIMPro\Core\Routing;

class Route
{
    protected string $method;
    protected string $uri;
    protected mixed $action;
    protected array $middleware = [];
    protected ?string $name = null;
    protected array $parameters = [];

    public function __construct(string $method, string $uri, mixed $action)
    {
        $this->method = strtoupper($method);
        $this->uri = '/' . trim($uri, '/');
        $this->action = $action;
    }

    public function middleware(array|string $middleware): static
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        $this->middleware = array_merge($this->middleware, $middlewares);
        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getAction(): mixed
    {
        return $this->action;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function matches(string $requestMethod, string $requestUri): bool
    {
        if ($this->method !== $requestMethod) {
            return false;
        }

        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $this->uri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            $this->parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
