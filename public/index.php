<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Database\PdoFactory;
use App\Http\ExceptionHandler;
use App\Http\Request;
use App\Http\Router;
use App\Repository\PdoCategoryRepository;
use App\View\SmartyFactory;
use App\View\SmartyTemplateRenderer;

$config = require dirname(__DIR__) . '/bootstrap.php';

$smarty = (new SmartyFactory(
    $config['view']['template_dir'],
    $config['view']['compile_dir'],
    $config['view']['cache_dir'],
))->create();
$smarty->assign('appName', $config['app']['name']);
$renderer = new SmartyTemplateRenderer($smarty);

$exceptionHandler = new ExceptionHandler($renderer, $config['app']['debug']);
set_exception_handler([$exceptionHandler, 'handleException']);
set_error_handler([$exceptionHandler, 'handleError']);

$pdo = (new PdoFactory(
    $config['db']['host'],
    $config['db']['port'],
    $config['db']['database'],
    $config['db']['user'],
    $config['db']['password'],
))->create();

$categoryRepository = new PdoCategoryRepository($pdo);

$controllers = [
    HomeController::class => new HomeController(
        $categoryRepository,
        $renderer,
        $config['blog']['home_posts_per_category'],
    ),
];

$router = new Router(require dirname(__DIR__) . '/config/routes.php', $controllers);
$router->dispatch(Request::fromGlobals())->send();
