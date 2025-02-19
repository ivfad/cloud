<?php

namespace App\Controllers;
use Core\Foundation\Controller;
use Core\Foundation\Helpers\Renderable;
use Core\Foundation\View;

class HomeController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->view = new View();
    }

    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        $this->view->setTemplate('index.view.php');

        return $this->view->render();
    }
}