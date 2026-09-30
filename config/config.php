<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);

return [
    'app' => [
        'name' => 'Блог',
        'debug' => getenv('APP_DEBUG') === '1',
    ],
    'db' => [
        'host' => (string) getenv('DB_HOST'),
        'port' => (int) getenv('DB_PORT'),
        'database' => (string) getenv('DB_NAME'),
        'user' => (string) getenv('DB_USER'),
        'password' => (string) getenv('DB_PASSWORD'),
    ],
    'blog' => [
        'home_posts_per_category' => 3,
    ],
    'view' => [
        'template_dir' => $rootDir . '/templates',
        'compile_dir' => $rootDir . '/var/smarty/compile',
        'cache_dir' => $rootDir . '/var/smarty/cache',
    ],
];
