<?php

namespace Tomvis\HIMPro\Core\Exceptions;

class LocalErrorBuffer
{
    protected string $bufferPath;
    protected int $maxEntries;

    public function __construct(?string $bufferPath = null, int $maxEntries = 500)
    {
        $this->bufferPath = $bufferPath ?: dirname(__DIR__, 3) . '/storage/logs/error_buffer.json';
        $this->maxEntries = $maxEntries;
    }

    public function push(array $eventPayload): bool
    {
        try {
            $dir = dirname($this->bufferPath);
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $entries = $this->all();
            $entries[] = array_merge($eventPayload, ['buffered_at' => date('Y-m-d H:i:s')]);

            // Bound buffer size to prevent disk overflow
            if (count($entries) > $this->maxEntries) {
                $entries = array_slice($entries, -$this->maxEntries);
            }

            return file_put_contents($this->bufferPath, json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function all(): array
    {
        if (!file_exists($this->bufferPath)) {
            return [];
        }

        $content = @file_get_contents($this->bufferPath);
        if (!$content) {
            return [];
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    public function flush(): array
    {
        $entries = $this->all();
        if (file_exists($this->bufferPath)) {
            @unlink($this->bufferPath);
        }
        return $entries;
    }

    public function count(): int
    {
        return count($this->all());
    }
}
