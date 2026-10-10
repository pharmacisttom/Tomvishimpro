<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'TomvisHIMPro',
    'env' => $_ENV['APP_ENV'] ?? 'local',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost/tomvishimpro/public',
    'timezone' => 'Asia/Bangkok',
    'locale' => 'th',
    'fallback_locale' => 'en',
    'secret' => $_ENV['APP_SECRET'] ?? 'default-secret-key-change-in-production-32chars',
];
