<?php

namespace Core\Middleware;

use Core\Response;
use Core\Role;

class User implements Role
{
    /**
     * @return void
     */
    public function handle(): void
    {
        if(!$_SESSION || !$_SESSION['user']) {
            Response::error(401, 'Authorization needed to view the content');
        }
    }
}
