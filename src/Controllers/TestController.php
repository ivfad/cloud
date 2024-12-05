<?php

namespace App\Controllers;

class TestController
{
    public function __construct()
    {

    }
    public function index()
    {
        echo 'TestController index';
        return 'TestController index return';
    }
}