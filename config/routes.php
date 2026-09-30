<?php

declare(strict_types=1);

use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Controller\PostController;

// Format: ['GET', '/path/{id}', [Controller::class, 'method']]
return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/category/{id}', [CategoryController::class, 'show']],
    ['GET', '/post/{id}', [PostController::class, 'show']],
];
