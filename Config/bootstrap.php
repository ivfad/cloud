<?php

use Config\DbConfig;
use Core\App;
use Core\Container\Container;
use Core\Database\Database;
use Psr\Container\ContainerExceptionInterface;

$container = Container::getInstance();

App::setContainer(Container::getInstance());

App::singleton(Database::class, Database::getInstance());

require_once BASE_PATH . 'Config/DbConfig.php';

try {
    $db = App::get(Database::class);
    $config = new DbConfig();
    $db->connect($config, username: 'root', password: '');
    $db->query($config->init());
} catch (ContainerExceptionInterface $e) {
    echo 'Container exception: ' . $e->getMessage();
} catch (PDOException $e) {
    echo 'PDOException: ' . $e->getMessage();
}

