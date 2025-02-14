<?php

namespace Core\Middleware;

class User
{
    public function handle(): void
    {
        if(!$_SESSION || !$_SESSION['user']) {
            header('location: /');
            die();
        }
    }
}
