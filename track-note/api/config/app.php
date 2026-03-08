<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'TrackNote',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',

    'session' => [
        'name' => 'tracknote_session',
        'lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 7200), // 2 hours
        'path' => '/',
        'domain' => getenv('SESSION_DOMAIN') ?: null,
        'secure' => filter_var(getenv('SESSION_SECURE') ?: false, FILTER_VALIDATE_BOOLEAN),
        'httponly' => true,
        'samesite' => 'Lax',
    ],

    'cors' => [
        'allowed_origins' => explode(',', getenv('CORS_ORIGINS') ?: 'http://localhost:5173'),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'allow_credentials' => true,
        'max_age' => 86400,
    ],

    'audit' => [
        'retention_days' => (int) (getenv('AUDIT_RETENTION_DAYS') ?: 365), // 1 year default
    ],
];
