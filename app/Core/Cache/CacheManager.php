<?php

namespace Tomvis\HIMPro\Core\Cache;

class CacheManager
{
    protected string $storageDir;
    protected static array $memoryCache = [];

    public function __construct(?string $storageDir = null)
    {
        $this->storageDir = $storageDir ?: dirname(__DIR__, 3) . '/storage/cache';
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0777, true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (isset(self::$memoryCache[$key])) {
            return self::$memoryCache[$key];
        }

        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            return $default;
        }

        $content = file_get_contents($file);
        $data = unserialize($content);

        if (isset($data['expires_at']) && time() > $data['expires_at']) {
            $this->forget($key);
            return $default;
        }

        self::$memoryCache[$key] = $data['value'] ?? $default;
        return self::$memoryCache[$key];
    }

    public function set(string $key, mixed $value, int $ttlSeconds = 3600): bool
    {
        self::$memoryCache[$key] = $value;
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttlSeconds,
        ];
        $file = $this->getFilePath($key);
        return file_put_contents($file, serialize($data)) !== false;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function forget(string $key): void
    {
        unset(self::$memoryCache[$key]);
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    public function flush(): void
    {
        self::$memoryCache = [];
        $files = glob($this->storageDir . '/*.cache');
        foreach ($files as $file) {
            @unlink($file);
        }
    }

    protected function getFilePath(string $key): string
    {
        return $this->storageDir . '/' . md5($key) . '.cache';
    }
}
