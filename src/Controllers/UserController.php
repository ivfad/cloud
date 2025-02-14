<?php

namespace App\Controllers;

use App\Models\UserModel;
use Core\App;
use Core\Controller;
use Core\Database;
use Core\Request;
use Core\Route;
use Core\View;
use DateTimeImmutable;
use JetBrains\PhpStorm\NoReturn;
use PDOException;
use Psr\Container\ContainerExceptionInterface;
use App\Controllers\Mailer;
use Core\Response;

class UserController extends Controller{

    function __construct()
    {
        parent::__construct();
        $this->model = new UserModel();
        $this->view = new View();
    }

    /**
     * @return array
     */
    public function list():array
    {
        return $this->model->getUsersList();
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    public function login(Request $request): mixed
    {
        $email = $request->post()['email'];
        $password = $request->post()['password'];

        $user = $this->model->getUserByEmail($email);

        if(!$user || !password_verify($password, $user['password'])) {
            Response::status(401);
            return 'There is no such user or password is incorrect';
        }

        $this->setSessionParams($user);
        session_regenerate_id(true);

        Response::redirect(303, 'location: /');

        return $user;
    }

    public function loginView(Request $request): mixed
    {
        $this->view->setTemplate('login.view.php');
        return $this->view->render();
    }

    /**
     * @param array $user
     * @return void
     */
    private function setSessionParams (array $user): void
    {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'admin' => $user['admin'],
        ];
    }

    /**
     * @return void
     */
    #[NoReturn] public function logout(): void
    {
        session_unset();
        session_destroy();
        $params = session_get_cookie_params();
        setcookie(name:session_name(), value:'', expires_or_options: time() - 3600, path:$params['path'], domain:$params['domain'], httponly: true);
        Response::status(204);
    }

    /**
     * @param Request $request
     * @param $params
     * @return mixed
     */
    public function get(Request $request, $params): mixed
    {
        $id = $params['id'];

        return $this->model->getUserInfoById($id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request): mixed
    {
        $initialEmail = $_SESSION['user']['email'];
        $email = $request->post()['email'] ?? null;
        $password = $request->post()['password'] ?? null;
        $name = $request->post()['name'] ?? null;
        $age = $request->post()['age'] ?? null;
        $gender = $request->post()['gender'] ?? null;

        $user = $this->model->updateUserInfo($name, $email, $password, $age, $gender, $initialEmail);

        $this->setSessionParams($user);

        return $user;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function reset_password(Request $request): void
    {
        try{
            App::bind(Mailer::class, function() {
                return new Mailer();
            });

            $mailer = App::get(Mailer::class);

            $name = isset($_SESSION['user']['name']) ?:'User';

            $content = [
                'address' => $_SESSION['user'][ 'email'],
                'name' => $name,
                'subject' => 'Link to change your password from Cloud storage',
                'body' => "Hi, " . $name . ", here is <a href=\"#\"> a link to change your password</a>",
                'altbody' => 'Here is a link to change your password',
            ];

            $mailer->send_email($content);
        } catch (ContainerExceptionInterface $e) {
            echo 'Container exception: ' . $e->getMessage();
        }
    }

    public function jwt()
    {
        require_once base_path('JWTCode.php');

        $issuedAt   = new DateTimeImmutable();

// Create token payload
        $payload = [
            'iss' => 'cloudstorage',
            'sub' => $_SESSION['user'][ 'id'],
            'admin' => $_SESSION['user'][ 'admin'],
            'iat' => $issuedAt->getTimestamp(),
            'exp' => $issuedAt->modify('+15 minutes')->getTimestamp()
        ];

        $accessToken = JWT::createAccessToken($_SESSION['user'][ 'id'], $_SESSION['user'][ 'admin']);

        $refreshToken = JWT::createRefreshToken($_SESSION['user'][ 'id'], $_SESSION['user'][ 'admin']);
//        $ans = [$accessToken, $refreshToken];
//        dd($ans);
//        dd(JWT::checkTokenExpired($refreshToken));
        if(JWT::verifyToken($accessToken, 'access') == false) {
            dd(JWT::verifyToken($accessToken, 'refresh'));
        } else {
            dd('Access JWT if ok');
        }

    }
}

