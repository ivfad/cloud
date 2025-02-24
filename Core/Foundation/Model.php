<?php

namespace Core\Foundation;

use Core\App;
use Core\Database\Database;
use Psr\Container\ContainerExceptionInterface;

abstract class Model
{
    protected Database $db;

    /**
     * @throws ContainerExceptionInterface
     */
    public function __construct()
    {
        $this->db = App::get(Database::class);
    }
}