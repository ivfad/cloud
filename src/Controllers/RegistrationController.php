<?php

namespace App\Controllers;
use App\Models\RegistrationModel;
use Core\Foundation\Controller;
use Core\Foundation\Helpers\Renderable;
use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;
use Core\Foundation\View;
use JetBrains\PhpStorm\NoReturn;

class RegistrationController extends Controller

{
    function __construct()
    {
        parent::__construct();
        $this->model = new RegistrationModel();
        $this->view = new View();
    }

    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        $this->view->setTemplate('register.view.php');
        return $this->view->render();
    }


    /**
     * @param Request $request
     * @return void
     */
    #[NoReturn] public function store(Request $request): void
    {
        $email = $request->post()['email'];

        $user = $this->model->getByEmail($email);

        if ($user) {
            Response::error(422, 'Such email is already in use');
        }

        $this->model->addUser($email, $request->post()['password']);

        Response::redirect(303, 'location: /login');
    }
}