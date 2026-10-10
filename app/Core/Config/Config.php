<?php

namespace Tomvis\HIMPro\Core\Config;

class Config
{
    protected static array $items = [];

    public static function loadFromDir(string $configDir): void
    {
        if (!is_dir($configDir)) {
            return;
        }

        $files = glob($configDir . '/*.php');
        foreach ($files as $file) {
            $key = pathinfo($file, PATHINFO_FILENAME);
            self::$items[$key] = require $file;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $array = self::$items;
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            foreach ($keys as $segment) {
                if (is_array($array) && array_key_exists($segment, $array)) {
                    $array = $array[$segment];
                } else {
                    return $default;
                }
            }
            return $array;
        }

        return self::$items[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $array = &self::$items;
        foreach ($keys as $segment) {
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }
            $array = &$array[$segment];
        }
        $array = $value;
    }

    public static function all(): array
    {
        return self::$items;
    }
}
