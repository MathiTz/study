<?php

declare(strict_types=1);

return [
    'driver' => 'pgsql',
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '5432',
    'database' => getenv('DB_DATABASE') ?: 'tracknote',
    'username' => getenv('DB_USERNAME') ?: 'postgres',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => 'utf8',
    'schema' => 'public',
    'sslmode' => getenv('DB_SSLMODE') ?: 'prefer',
];
