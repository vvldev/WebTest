<?php

declare(strict_types=1);

use App\Http\ExceptionHandler;
use App\Http\Request;
use App\Http\Router;
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

$router = new Router(require dirname(__DIR__) . '/config/routes.php', []);
$router->dispatch(Request::fromGlobals())->send();
