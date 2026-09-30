<?php

declare(strict_types=1);

use App\Controller\CategoryController;
use App\Controller\HomeController;

// Format: ['GET', '/path/{id}', [Controller::class, 'method']]
return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/category/{id}', [CategoryController::class, 'show']],
];
