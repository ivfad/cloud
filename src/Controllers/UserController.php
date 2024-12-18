<?php

namespace App\Controllers;

use App\Models\UserModel;
use Core\App;
use Core\Controller;
use Core\Database;
use Core\Request;
use Core\Route;
use Core\View;
use JetBrains\PhpStorm\NoReturn;
use Psr\Container\ContainerExceptionInterface;


class UserController extends Controller{

    function __construct()
    {
        parent::__construct();
        $this->model = new UserModel();
        $this->view = new View();
    }

    public function list():array
    {
        return $this->model->getUsersList();
    }

    public function login(Request $request)
    {
        $email = $request->post()['email'];
        $password = $request->post()['password'];

        $user = $this->model->getUserByEmail($email);

        if(!$user) {
            return 'There is no such user';
        }

        if(!password_verify($password, $user['password'])) {
            return 'Incorrect password';
        }
//        dd(session_status());
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $email,
            'admin' => $user['admin'],
        ];

        session_regenerate_id(true);

        header('location: /');
        exit();
    }

    #[NoReturn] public function logout()
    {
        session_destroy();
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain']);

        header('location: /');
        exit();
    }

    public function loginView(Request $request)
    {
        return require_once base_path('login.view.php');
    }

    public function get(Request $request, $params)
    {
        $id = $params['id'];

        return $this->model->getUserInfoById($id);
    }

    public function test(Request $request, $params)
    {
        $id = $params['id'];
        $user = $params['user'];
        echo 'id = ' . $id . ' user =' . $user . PHP_EOL;
    }

    public function updateView(Request $request)
    {
        return require_once base_path('update.view.php');
    }

    public function update(Request $request)
    {
//        dd($request);
    dd($request->post());
//        $id = $params['id'];
//        updateUserInfo
//        dd($_SESSION['user']['id']);
    }

    public function reset_password(Request $request)
    {
//        return require_once base_path('src/Controllers/mailer.php');

        try{
            App::bind(Mailer::class, function() {
                return new Mailer();
            });
            $mailer = App::get(Mailer::class);
            $content = [
//                'address' => 'ivfad91@gmail.com',
                'address' => $_SESSION['user'][ 'email'],
//                'name' => $_SESSION['user']['email'] ?:'User',
                'name' => 'User',
                'subject' => 'Link to change your password from Cloud storage',
                'body' => '<a href="#">Link to change your password</a>',
                'altbody' => 'Here is a link to change your password',
                ];
            $mailer->send_email($content);

        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }


    }
}

