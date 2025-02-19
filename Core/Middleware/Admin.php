<?php

namespace Core\Middleware;

use Core\Foundation\Http\Response;

class Admin implements Role
{
    /**
     * @return void
     */
    public function handle(): void
    {
        if(!$_SESSION || !$_SESSION['user']['admin']) {
            Response::error(403, 'No permission to view the content');
        }
    }
}