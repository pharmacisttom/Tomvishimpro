<?php

namespace Tomvis\HIMPro\Core\Logging;

use Tomvis\HIMPro\Core\Config\Config;

class Logger
{
    protected string $logPath;

    public function __construct(?string $logPath = null)
    {
        $this->logPath = $logPath ?: 'd:/xampp/htdocs/tomvishimpro/storage/logs/app.log';
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function audit(string $message, array $context = []): void
    {
        $auditPath = 'd:/xampp/htdocs/tomvishimpro/storage/logs/audit.log';
        $this->writeLog($auditPath, 'AUDIT', $message, $context);
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        $this->writeLog($this->logPath, $level, $message, $context);
    }

    protected function writeLog(string $filePath, string $level, string $message, array $context = []): void
    {
        $sanitizedContext = $this->sanitizeContext($context);
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($sanitizedContext) ? ' ' . json_encode($sanitizedContext, JSON_UNESCAPED_UNICODE) : '';
        $line = "[{$timestamp}] [{$level}] {$message}{$contextString}" . PHP_EOL;

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($filePath, $line, FILE_APPEND | LOCK_EX);
    }

    protected function sanitizeContext(array $context): array
    {
        $sensitiveKeys = Config::get('security.sensitive_keys', ['password', 'secret', 'token', 'hn', 'cid']);
        $sanitized = [];

        foreach ($context as $key => $value) {
            $lowerKey = strtolower($key);
            $isSensitive = false;
            foreach ($sensitiveKeys as $sKey) {
                if (str_contains($lowerKey, strtolower($sKey))) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $sanitized[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeContext($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
