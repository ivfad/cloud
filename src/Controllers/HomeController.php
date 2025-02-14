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

//        $this->view->render() = require_once base_path('index.view.php');
//        dd($this->view);
//        return 52;
        $this->view->setTemplate('index.view.php');
        return $this->view->render();

//        dd($this->view->render());
//        $view = require_once base_path('index.view.php');
//        exit();
    }
}