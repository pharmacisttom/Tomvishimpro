<?php

namespace Tomvis\HIMPro\Core\Request;

use Tomvis\HIMPro\Core\Config\Config;

class Request
{
    protected array $query;
    protected array $request;
    protected array $server;
    protected array $cookies;
    protected array $files;
    protected ?array $json = null;

    public function __construct(
        array $query = [],
        array $request = [],
        array $server = [],
        array $cookies = [],
        array $files = []
    ) {
        $this->query = $query;
        $this->request = $request;
        $this->server = $server;
        $this->cookies = $cookies;
        $this->files = $files;
    }

    public static function capture(): static
    {
        return new static($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
    }

    public function getMethod(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($this->request['_method'])) {
            return strtoupper($this->request['_method']);
        }
        return $method;
    }

    public function getUri(): string
    {
        // 1. Front Controller Fallback Routing (?route=/dashboard)
        if (isset($this->query['route']) && !empty($this->query['route'])) {
            $route = '/' . trim((string)$this->query['route'], '/');
            return $route === '/' ? '/' : $route;
        }

        // 2. Standard Request URI
        $uri = $this->server['REQUEST_URI'] ?? '/';
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = '/' . trim($uri, '/');

        // 3. Subdirectory Base Path Resolution (e.g. /tomvishimpro/public or /tomvishimpro)
        $appUrl = Config::get('app.url', '');
        if (!empty($appUrl)) {
            $parsedUrl = parse_url($appUrl);
            $basePath = rtrim($parsedUrl['path'] ?? '', '/');
            if (!empty($basePath) && str_starts_with($uri, $basePath)) {
                $uri = substr($uri, strlen($basePath));
                $uri = '/' . trim($uri, '/');
            }
        }

        return $uri === '/' ? '/' : $uri;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->request, $this->json() ?? []);
    }

    public function json(): ?array
    {
        if ($this->json === null) {
            $content = file_get_contents('php://input');
            if ($content && str_contains($this->header('Content-Type', ''), 'application/json')) {
                $decoded = json_decode($content, true);
                $this->json = is_array($decoded) ? $decoded : [];
            } else {
                $this->json = [];
            }
        }
        return $this->json;
    }

    public function header(string $key, ?string $default = null): ?string
    {
        $normalizedKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        if (isset($this->server[$normalizedKey])) {
            return $this->server[$normalizedKey];
        }
        if (isset($this->server[strtoupper($key)])) {
            return $this->server[strtoupper($key)];
        }
        return $default;
    }

    public function isJson(): bool
    {
        return str_contains($this->header('Accept', ''), 'application/json') ||
               str_contains($this->header('Content-Type', ''), 'application/json');
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization');
        if ($header && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }
}
