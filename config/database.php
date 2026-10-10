<?php

return [
    'default' => $_ENV['DB_CONNECTION'] ?? 'mysql',

    'central_host' => '192.168.111.240',

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '192.168.111.240',
            'port' => (int)($_ENV['DB_PORT'] ?? 3306),
            'database' => $_ENV['DB_DATABASE'] ?? 'tomvishimpro_dev',
            'username' => $_ENV['DB_USERNAME'] ?? 'tomvis_dev_app',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
            ],
        ],

        'app' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '192.168.111.240',
            'port' => (int)($_ENV['DB_PORT'] ?? 3306),
            'database' => $_ENV['DB_DATABASE'] ?? 'tomvishimpro_dev',
            'username' => $_ENV['DB_USERNAME'] ?? 'tomvis_dev_app',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
            ],
        ],

        'his_readonly' => [
            'driver' => 'mysql',
            'host' => $_ENV['HIMPRO_HOST'] ?? '192.168.111.240',
            'port' => (int)($_ENV['HIMPRO_PORT'] ?? 3306),
            'database' => $_ENV['HIMPRO_DATABASE'] ?? 'himpro_his',
            'username' => $_ENV['HIMPRO_USERNAME'] ?? 'tomvis_his_readonly',
            'password' => $_ENV['HIMPRO_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 3,
            ],
        ],

        'sandbox' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '192.168.111.240',
            'port' => (int)($_ENV['DB_PORT'] ?? 3306),
            'database' => $_ENV['DB_DATABASE'] ?? 'tomvishimpro_dev',
            'username' => $_ENV['DB_USERNAME'] ?? 'tomvis_dev_app',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
    ],
];
