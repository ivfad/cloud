<?php

declare(strict_types=1);

use Core\Router;
use Core\Request;
use Core\Response;
const BASE_PATH = __DIR__ . '/../';

session_start();

require_once BASE_PATH . '/vendor/autoload.php';
require_once base_path('bootstrap.php');

$router = new Router();

$request = Request::createFromGlobals();
$content = $router->route($request);
//dd(2);
$response = new Response($content);
//dd($response);
//dd($content);
//$response->json()->send();
//$response->send();
//dd(122);

$response->json();
$response->send();


