<?php

declare(strict_types=1);

use App\Controller\HomeController;

// Format: ['GET', '/path/{id}', [Controller::class, 'method']]
return [
    ['GET', '/', [HomeController::class, 'index']],
];
