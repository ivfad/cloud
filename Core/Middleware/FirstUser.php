<?php

namespace Core\Middleware;

use Core\Foundation\Http\Response;

class FirstUser implements Role
{
    /**
     * @return void
     */
    public function handle(): void
    {
        if (!$_SESSION['firstUser']) {
            Response::error(404, 'Page does not exist');
        }
    }
}