<?php

namespace Core\Middleware;

use Core\Response;
use Core\Role;

class Guest implements Role
{
    /**
     * @return void
     */
    public function handle(): void
    {
        if($_SESSION['user'] ?? false) {
            Response::redirect(303, 'location: /');
        }
    }
}