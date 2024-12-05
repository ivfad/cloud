<?php

namespace Core\Middleware;

class User
{
    public function handle(): void
    {
//        $user = $_SESSION ? $_SESSION['user'] : false;
//        dd($_SESSION ? $_SESSION['user'] : false);
        if(!$_SESSION || !$_SESSION['user']) {
            header('location: /');
            die();
        }
    }
}