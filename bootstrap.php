<?php

use Core\Container;
use Core\Database;
use Core\App;
use Psr\Container\ContainerExceptionInterface;

$container = Container::getInstance();

App::setContainer(Container::getInstance());

App::singleton(Database::class, Database::getInstance());

require_once base_path('Config.php');

try {
    $db = App::get(Database::class);
    $config = new Config();
    $db->connect($config, username: 'root', password: '');
} catch (ContainerExceptionInterface $e) {
    echo 'Container exception: ' . $e->getMessage();
} catch (PDOException $e) {
    echo 'PDOException: ' . $e->getMessage();
}

//require_once base_path('Core/TestScenarios.php');

//require_once base_path('Core/Model.php');
//require_once base_path('Core/View.php');
require_once base_path('Core/Controller.php');
//dd($db);


