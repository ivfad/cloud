<?php

namespace App\Controllers;

use App\Models\UserModel;
use Core\App;
use Core\Foundation\Controller;
use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;
use Core\Foundation\View;
use Core\Helpers\Renderable;
use Psr\Container\ContainerExceptionInterface;

class RegistrationController extends Controller
{
    private UserModel $userModel;

    function __construct()
    {
        parent::__construct();
        try {
            $this->userModel = App::get(UserModel::class);
        } catch (ContainerExceptionInterface $e) {
            Response::error(500, $e->getMessage());
        }
        $this->view = new View();
    }

    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        $this->view->setTemplate('register.view.php');
        $params = [
            'title' => 'Register on Cloud-Storage',
            'buttonText' => 'Register',
        ];

        return $this->view->render($params);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function store(Request $request): void
    {
        $email = $request->post()['email'];
        $password = $request->post()['password'];
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        if (!$this->userModel->hasEntries('user')) {
            $this->userModel->addUser($email, $passwordHash, 1);
            $_SESSION['firstUser'] = true;
            Response::redirect(303, 'location: /greeting');
        }

        $user = $this->userModel->getOneBy('email', $email, 'user');

        if ($user) {
            Response::error(422, 'Such email is already in use');
        }

        $this->userModel->addUser($email, $passwordHash);

        Response::redirect(303, 'location: /login');
    }

    public function greeting(): Renderable
    {
        $this->view->setTemplate('greeting.view.php');
        $params = [
            'title' => 'Welcome to Cloud-Storage',
            'buttonText' => 'Proceed to log in',
        ];

        return $this->view->render($params);
    }
}