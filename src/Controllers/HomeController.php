<?php

namespace App\Controllers;
use Core\Controller;
use Core\View;

class HomeController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->view = new View();
    }

    public function index()
    {
//        dd($this->view);
//        dd($this->view);
//        $this->view->render() = require_once base_path('index.view.php');
        $view = require_once base_path('index.view.php');
        exit();
    }
}