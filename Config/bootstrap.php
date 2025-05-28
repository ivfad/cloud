<?php

use Config\DbConfig;
use Core\App;
use Core\Cache\RedisService;
use Core\Container\Container;
use Core\Database\Database;
use Core\Foundation\Http\Response;
use Psr\Container\ContainerExceptionInterface;

try {
    $container = Container::getInstance();

    App::setContainer(Container::getInstance());
    App::singleton(Database::class, Database::getInstance());

    setEnv(BASE_PATH . 'Config/.env');
    require_once BASE_PATH . 'Config/DbConfig.php';
    $db = App::get(Database::class);
    $config = new DbConfig(host: getenv("DB_HOST"), port: getenv("DB_PORT"), dbname: getenv("DB_NAME"), charset: getenv("DB_CHARSET"));

    App::singleton(RedisService::class, RedisService::getInstance());

    $db->connect($config, username: getenv("DB_USER"), password: getenv("DB_PASS"));
    $db->query($config->init());

} catch (ContainerExceptionInterface | Exception $e) {
    Response::error(500, $e->getMessage());
}

/**
 * Installs main settings for the database and mailer according to Config/.env file
 * @param string $filePath
 * @return void
 * @throws Exception
 */
function setEnv(string $filePath = ''):void
{
    if (empty($filePath)) {
        $filePath = BASE_PATH . '.env';
    }

    if (!file_exists($filePath) ) {
        throw new Exception( 'Wrong path of .env file' );
    }

    $data = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (empty($data)) return;

    foreach ($data as $line) {
        if (empty($line) || str_starts_with(trim($line), '#')) continue;

        list($key, $value) = array_map('trim', explode('=', $line, 2));

        if(!isset($key)) continue;

        putenv("$key=$value");
    }
}
