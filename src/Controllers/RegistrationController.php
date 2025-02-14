<?php

namespace App\Controllers;
use Core\App;
use Core\Controller;
use Core\Database;
use Core\Request;
use Core\Response;
use Core\View;
use JetBrains\PhpStorm\NoReturn;

class RegistrationController extends Controller

{
    function __construct()
    {
        parent::__construct();
        $this->view = new View();
    }

    public function index()
    {
        $this->view->setTemplate('register.view.php');
        return $this->view->render();
    }

    /**
     * @throws \Core\Exceptions\ContainerException
     * @throws \Core\Exceptions\ContainerNotFoundException
     */
    #[NoReturn] public function store(Request $request)
    {
        $db = App::get(Database::class);

        $email = $request->post()['email'];

        $user = $db->query('SELECT * FROM `user` where email = :email', [
            ':email' => $email,
        ])->find();

        if ($user) {
            Response::error(422, 'Such email is already in use');
        }

        $db->query('INSERT INTO `user`(email, password) VALUES(:email, :password)', [
            ':email' => $email,
            ':password' => password_hash($request->post()['password'], PASSWORD_BCRYPT),
        ]);
//
//        $_SESSION['user'] = [
//            'email' => $email,
//        ];

        Response::redirect(303, 'location: /login');
    }
}