<?php

namespace App\Controllers;

use App\Models\UserModel;
use Core\App;
use Core\Controller;
//use Core\Database;
use Core\Renderable;
use Core\Request;
//use Core\Route;
use Core\View;
use DateTimeImmutable;
use JetBrains\PhpStorm\NoReturn;
//use PDOException;
use Psr\Container\ContainerExceptionInterface;
//use App\Controllers\Mailer;
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
        $users = $this->model->getUsersList();

        if (empty($users)){
            Response::error(404, 'No appropriate data found in database');
        }

        return $users;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function login(Request $request): string
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
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function loginView(Request $request): Renderable
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
        $info = $this->model->getUserInfoById($id);

        if (!$info) {
            Response::error('404', 'No appropriate data found in database');
        }

        return $info;
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

        if (!$this->model->getUserByEmail($initialEmail)) {
            Response::error(400, 'No such user, please re-login');
        }

        if (empty($email) || empty($password)) {
            Response::error(400, 'Main fields are not filled in');
        }

        if ($email != $initialEmail && $this->model->getUserByEmail($email)) {
            Response::error(422, 'Such email is already in use');
        }

        $user = $this->model->updateUserInfo($name, $email, $password, $age, $gender, $initialEmail);

        $this->setSessionParams($user);

        return $user;
    }

    /**
     * @return Renderable
     */
    public function updateView(): Renderable
    {
        $this->view->setTemplate('update.view.php');
        return $this->view->render();
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

