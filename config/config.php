<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);

return [
    'app' => [
        'name' => 'Блог',
        'debug' => getenv('APP_DEBUG') === '1',
    ],
    'view' => [
        'template_dir' => $rootDir . '/templates',
        'compile_dir' => $rootDir . '/var/smarty/compile',
        'cache_dir' => $rootDir . '/var/smarty/cache',
    ],
];
