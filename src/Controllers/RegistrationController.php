<?php

namespace App\Controllers;
use Core\App;
use Core\Controller;
use Core\Database;
use Core\Request;

class RegistrationController extends Controller
{
    public function index()
    {
        require_once base_path('register.view.php');
    }

    public function store(Request $request)
    {
        $db = App::get(Database::class);

        $email = $request->post()['email'];

        $user = $db->query('SELECT * FROM `user` where email = :email', [
            ':email' => $email,
        ])->find();

        if (!$user) {
            $db->query('INSERT INTO `user`(email, password) VALUES(:email, :password)', [
                ':email' => $email,
                ':password' => password_hash($request->post()['password'], PASSWORD_BCRYPT),
            ]);

            $_SESSION['user'] = [
                'email' => $email,
            ];
        }

        header('location: /');
        exit();
    }
}