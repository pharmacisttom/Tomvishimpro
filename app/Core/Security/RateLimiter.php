<?php

namespace Tomvis\HIMPro\Core\Security;

class RateLimiter
{
    protected static array $hits = [];

    public static function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        $attempts = self::attempts($key);
        return $attempts >= $maxAttempts;
    }

    public static function hit(string $key, int $decaySeconds = 60): int
    {
        SessionManager::start();
        $records = SessionManager::get('_rate_limits', []);
        $now = time();

        if (isset($records[$key])) {
            if ($now - $records[$key]['reset_at'] > 0) {
                $records[$key] = [
                    'hits' => 1,
                    'reset_at' => $now + $decaySeconds,
                ];
            } else {
                $records[$key]['hits']++;
            }
        } else {
            $records[$key] = [
                'hits' => 1,
                'reset_at' => $now + $decaySeconds,
            ];
        }

        SessionManager::set('_rate_limits', $records);
        return $records[$key]['hits'];
    }

    public static function attempts(string $key): int
    {
        SessionManager::start();
        $records = SessionManager::get('_rate_limits', []);
        if (isset($records[$key]) && time() < $records[$key]['reset_at']) {
            return $records[$key]['hits'];
        }
        return 0;
    }

    public static function clear(string $key): void
    {
        SessionManager::start();
        $records = SessionManager::get('_rate_limits', []);
        unset($records[$key]);
        SessionManager::set('_rate_limits', $records);
    }
}
