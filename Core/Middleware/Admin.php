<?php

namespace Core\Middleware;

class Admin
{
    public function handle(): void
    {
        if(!$_SESSION || !$_SESSION['user']['admin']) {
            header('location: /');
            die();
        }
    }
}