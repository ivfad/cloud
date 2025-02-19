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
    $initQuery = "CREATE TABLE IF NOT EXISTS `user` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `admin` tinyint(1) NOT NULL,
            `age` tinyint(3) DEFAULT NULL,
            `gender` char(1) DEFAULT NULL,
            `password` varchar(255) NOT NULL,
            UNIQUE KEY `id` (`id`),
            UNIQUE KEY `user_email_idx` (`email`) USING BTREE)
            ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $db->query($initQuery);
} catch (ContainerExceptionInterface $e) {
    echo 'Container exception: ' . $e->getMessage();
} catch (PDOException $e) {
    echo 'PDOException: ' . $e->getMessage();
}



