<?php

namespace App\Controllers;

use App\Models\AdminModel;
use Core\App;
use Core\Controller;
use Core\Database;
use Core\Request;
use Core\Response;
use Core\Route;
use Core\View;
use JetBrains\PhpStorm\NoReturn;
use Psr\Container\ContainerExceptionInterface;


class AdminController extends Controller{

    function __construct()
    {
        parent::__construct();
        $this->model = new AdminModel();
        $this->view = new View();
    }

    public function list():array
    {
        return $this->model->getUsersList();
    }

    public function get(Request $request, $params)
    {
        $id = $params['id'];

        return $this->model->getUserInfoById($id);
    }

    public function delete(Request $request, $params):void
    {
        $id = $params['id'];

        if ($this->model->deleteUserById($id)) {
            Response::status(204);
        }
        return;
    }

    public function update(Request $request, $params)
    {
        $id = $params['id'];
        $updateInfo = [
            'name' => $request->post()['name'] ?? null,
            'email' => $request->post()['email'] ?? null,
            'admin' => $request->post()['admin'] ?? null,
            'age' => $request->post()['age'] ?? null,
            'gender' => $request->post()['gender'] ?? null,
            'password' => $request->post()['password'] ?? null,
        ];

        if (empty($updateInfo['email']) || empty($updateInfo['password'])) {
            exit();
        }
        if ($this->model->updateUserInfoById($id, $updateInfo)) {
            Response::status(204);
        }
//    return $this->model->updateUserInfoById($updateInfo);

//        $user = $this->model->getUserByEmail($initialEmail);

//
//    return $this->model->getUserByEmail($email);
//        header('location: /');
//        exit();

    }

}

