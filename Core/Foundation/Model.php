<?php

namespace Core\Foundation;

use Core\App;
use Core\Database\Database;
use Core\Exceptions\ContainerException;
use Core\Exceptions\ContainerNotFoundException;

abstract class Model
{
    protected Database $db;
    /**
     * @throws ContainerException
     * @throws ContainerNotFoundException
     */
    public function __construct()
    {
        $this->db = App::get(Database::class);
    }
}