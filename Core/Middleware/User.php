<?php

namespace Core\Middleware;

use Core\Exceptions\AuthenticationException;

class User implements Role
{
    /**
     * @return void
     * @throws AuthenticationException
     */
    public function handle(): void
    {
        if (!$_SESSION || !$_SESSION['user']) {
            throw new AuthenticationException("Authentication  needed to view the content");
        }
    }
}