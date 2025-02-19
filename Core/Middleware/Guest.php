<?php

namespace Core\Middleware;

use Core\Foundation\Http\Response;

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