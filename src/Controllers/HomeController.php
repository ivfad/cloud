<?php

namespace App\Controllers;

use Core\Foundation\Controller;
use Core\Foundation\View;
use Core\Helpers\Renderable;

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
    /**
     * @return Renderable
     */
    public function index2(): Renderable
    {
        $this->view->setTemplate('index2.view.php');

        return $this->view->render();
    }
}