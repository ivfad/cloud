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

        return $user; //??
        header('location: /');
        exit();
    }

    #[NoReturn] public function logout()
    {
        session_destroy();
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain']);
        return $params ;
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
        $db = App::get(Database::class);
        $initialEmail = $_SESSION['user']['email'];
        $user = $this->model->getUserByEmail($initialEmail);

        if ($user) {

        //dd(file_get_contents('php://input'));
        $name = $request->post()['name'] ?? null;
        $email = $request->post()['email'] ?? null;
        $password = $request->post()['password'] ?? null;
        $age = $request->post()['age'] ?? null;
        $gender = $request->post()['gender'] ?? null;

        if (empty($email) || empty($password)) {
            exit();
        }

        if ($email != $initialEmail && $this->model->getUserByEmail($email)) {
            exit();
        }

            $db->query('UPDATE `user` 
                            SET 
                                name = :name, 
                                email = :email,
                                password = :password,
                                age = :age,
                                gender = :gender 
                        WHERE email = :initialEmail', [
            ':initialEmail' => $initialEmail,
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($request->post()['password'], PASSWORD_BCRYPT),
            ':age' => $age,
            ':gender' => $gender
            ]);
//            dd($this->getUserByEmail($email));
        }

//            dd($user);
    return $this->model->getUserByEmail($email);
//        header('location: /');
//        exit();

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
                'address' => 'ENTER@EMAIL.com',
//                'address' => $_SESSION['user'][ 'email'],
//                'name' => $_SESSION['user']['email'] ?:'User',
                'name' => 'User',
                'subject' => 'Link to change your password from Cloud storage',
                'body' => '<a href="#">Link to change your password</a>',
                'altbody' => 'Here is a link to change your password',
                ];
            $mailer->send_email($content);
            dd(223);

        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
//            dd(555);
    }

    public function jwt()
    {
        App::bind(JWT::class, function() {
            return new JWT('A586E3272357538782F413F4428472B4B6250655367566B5970337336763979');
        });
        $jwt = App::get(JWT::class);
        $token = $jwt->encode(['user_id' => 12]);



        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

// Create token payload as a JSON string
        $payload = json_encode(['user_id' => 12]);
        $payload = json_encode([
            'sub' => "1234",
            'name' => "1230",
            'iat' => "1234",
        ]);
//        dd($payload);
// Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

// Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

// Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);

// Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

// Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
dd($jwt);
        echo $jwt;
    }
}

