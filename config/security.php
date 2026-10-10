<?php

return [
    'csrf' => [
        'enabled' => true,
        'token_name' => '_token',
        'header_name' => 'X-CSRF-TOKEN',
    ],
    'headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;",
    ],
    'rate_limit' => [
        'api_max_requests' => 60,
        'api_decay_minutes' => 1,
        'login_max_attempts' => 5,
        'login_decay_minutes' => 15,
    ],
    'sensitive_keys' => [
        'password', 'password_confirmation', 'secret', 'api_key', 'token', 'access_token', 'credit_card'
    ],
];
