<?php

namespace Tomvis\HIMPro\Core\Exceptions;

class ErrorFingerprintService
{
    /**
     * Generate a deterministic 64-character SHA-256 fingerprint for an error event.
     */
    public static function generateFingerprint(
        string $exceptionClass,
        ?string $code,
        string $sourceComponent,
        string $stackTrace,
        string $frameworkVersion = '3.0.0',
        string $moduleVersion = '1.0.0'
    ): string {
        $normalizedClass = trim($exceptionClass);
        $normalizedCode = $code !== null ? trim($code) : '0';
        $normalizedComponent = trim($sourceComponent);
        $normalizedTrace = self::extractStackFrameSignature($stackTrace);

        $payload = sprintf(
            '%s|%s|%s|%s|%s|%s',
            $normalizedClass,
            $normalizedCode,
            $normalizedComponent,
            $normalizedTrace,
            $frameworkVersion,
            $moduleVersion
        );

        return hash('sha256', $payload);
    }

    /**
     * Extracts top non-variable stack frame signatures (function names and file basenames).
     */
    public static function extractStackFrameSignature(string $stackTrace): string
    {
        $lines = explode("\n", str_replace("\r", "", $stackTrace));
        $frames = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Remove memory addresses, absolute path prefixes, line numbers variation
            $normalizedLine = preg_replace('#[A-Za-z]:/[^:\n]+/#', '', $line);
            $normalizedLine = preg_replace('/:\d+/', '', $normalizedLine);
            $normalizedLine = preg_replace('/\(0x[0-9a-fA-F]+\)/', '', $normalizedLine);

            $frames[] = $normalizedLine;
            if (count($frames) >= 4) {
                break;
            }
        }

        return implode('||', $frames);
    }

    /**
     * Classify defect type based on error characteristics and history.
     */
    public static function classifyCategory(string $sourceComponent, bool $isNewGroup, bool $isResolvedBefore, int $affectedAppsCount = 1): string
    {
        if (str_contains($sourceComponent, 'Tomvis\\HIMPro\\Core')) {
            if ($isResolvedBefore) {
                return 'Regression';
            }
            return 'Framework Core Defect';
        }

        if (str_contains($sourceComponent, 'PDO') || str_contains($sourceComponent, 'Database') || str_contains($sourceComponent, 'Connection')) {
            return 'Infrastructure Incident';
        }

        if ($isResolvedBefore) {
            return 'Regression';
        }

        if (!$isNewGroup) {
            return 'Recurring Issue';
        }

        return 'Module-Specific Defect';
    }
}
