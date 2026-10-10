<?php

namespace Tomvis\HIMPro\Core\Exceptions;

class ErrorSanitizer
{
    protected static array $sensitiveKeys = [
        'password', 'pass', 'secret', 'token', 'authorization', 'api_key', 'apikey',
        'cookie', 'session_id', 'hn', 'an', 'cid', 'national_id', 'patient_name',
        'first_name', 'last_name', 'dob', 'card_id', 'credit_card', 'ssn'
    ];

    /**
     * Sanitize plain text string (e.g. error message, stack trace) removing PHI and Secrets.
     */
    public static function sanitizeText(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        // 1. Redact Authorization / Bearer Tokens / API Keys
        $text = preg_replace('/(Authorization:\s*Bearer\s+)[A-Za-z0-9\-\._~\+\/]+=*/i', '$1[REDACTED]', $text);
        $text = preg_replace('/(api[-_]?key|secret|password|token)\s*=\s*[\'"][^\'"]+[\'"]/i', '$1="[REDACTED]"', $text);
        $text = preg_replace('/(api[-_]?key|secret|password|token)\s*:\s*[\'"][^\'"]+[\'"]/i', '$1: "[REDACTED]"', $text);

        // 2. Redact Thai National ID (13 continuous digits or with dashes X-XXXX-XXXXX-XX-X)
        $text = preg_replace('/\b[1-9]\d{12}\b/', '[REDACTED_NATIONAL_ID]', $text);
        $text = preg_replace('/\b[1-9]-\d{4}-\d{5}-\d{2}-\d\b/', '[REDACTED_NATIONAL_ID]', $text);

        // 3. Redact HN (Hospital Number) patterns (e.g. HN: 66-123456, HN: 1234567, hn=660001)
        $text = preg_replace('/\b(HN|AN)\s*[:=]\s*\d{2}-\d{4,7}\b/i', '$1: [REDACTED_HN]', $text);
        $text = preg_replace('/\b(HN|AN)\s*[:=]\s*\d{5,10}\b/i', '$1: [REDACTED_HN]', $text);

        // 4. Redact Thai Patient Name prefixes (นาย, นาง, นางสาว, ด.ช., ด.ญ., พญ., นพ.) followed by names
        $text = preg_replace('/(นาย|นาง|นางสาว|ด\.ช\.|ด\.ญ\.|นพ\.|พญ\.)\s*\p{Thai}+\s+\p{Thai}+/u', '[REDACTED_PATIENT_NAME]', $text);

        // 5. Normalize File Paths to prevent local directory leakage
        $text = str_replace('\\', '/', $text);
        $text = preg_replace('#[A-Z]:/[^:\n]+/(app|src|vendor|config|routes)#i', '$1', $text);

        return $text;
    }

    /**
     * Recursively sanitize data array (e.g., request body, headers, context).
     */
    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string)$key);
            $isSensitive = false;

            foreach (self::$sensitiveKeys as $sKey) {
                if (str_contains($lowerKey, $sKey)) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = self::sanitizeText($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
