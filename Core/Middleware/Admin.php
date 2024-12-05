<?php

namespace Core\Middleware;

class Admin
{
    public function handle(): void
    {

//dd($_SESSION['user']);
//        if(!$_SESSION || !$_SESSION['user'] || !$_SESSION['user']['admin']) {
        if(!$_SESSION['user']['admin']) {
            header('location: /');
            die();
        }
    }
}