<?php

namespace Core\Middleware;

use Core\Exceptions\AuthorizationException;

class Admin implements Role
{
    /**
     * @return void
     * @throws AuthorizationException
     */
    public function handle(): void
    {
        if (!$_SESSION || !$_SESSION['user']['admin']) {
            throw new AuthorizationException("No permission to view the content");
        }
    }
}