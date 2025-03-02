<?php

use Config\DbConfig;
use Core\App;
use Core\Container\Container;
use Core\Database\Database;
use Core\Foundation\Http\Response;
use Psr\Container\ContainerExceptionInterface;

try {
    $container = Container::getInstance();

    App::setContainer(Container::getInstance());
    App::singleton(Database::class, Database::getInstance());

    require_once BASE_PATH . 'Config/DbConfig.php';

    $db = App::get(Database::class);
    $config = new DbConfig();

    $db->connect($config, username: 'root', password: '');
    $db->query($config->init());
} catch (ContainerExceptionInterface | Exception $e) {
    Response::error(500, $e->getMessage());
}

