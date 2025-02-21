<?php

declare(strict_types=1);

use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;
use Core\Router\Router;

const BASE_PATH = __DIR__ . '/../';

session_start();

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . 'Config/bootstrap.php';

$router = new Router();

$request = Request::createFromGlobals();
$content = $router->dispatch($request);

Response::setContent($content);
Response::send();
