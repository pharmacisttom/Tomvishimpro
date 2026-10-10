<?php

namespace Tomvis\HIMPro\Core\Support;

class FeatureFlag
{
    protected static array $flags = [
        'developer_studio' => true,
        'crud_generator' => true,
        'api_generator' => true,
        'report_builder' => true,
        'security_lab' => true,
        'fhir_interoperability' => true,
        'pharmacy_queue_module' => true,
    ];

    public static function isEnabled(string $flag): bool
    {
        return self::$flags[$flag] ?? false;
    }

    public static function enable(string $flag): void
    {
        self::$flags[$flag] = true;
    }

    public static function disable(string $flag): void
    {
        self::$flags[$flag] = false;
    }

    public static function all(): array
    {
        return self::$flags;
    }
}
