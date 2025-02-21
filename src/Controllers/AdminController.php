<?php

namespace App\Controllers;

use App\Models\AdminModel;
use Core\App;
use Core\Exceptions\ContainerException;
use Core\Exceptions\ContainerNotFoundException;
use Core\Foundation\Controller;
use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;
use Core\Foundation\View;


class AdminController extends Controller{

    function __construct()
    {
        parent::__construct();
        $this->model = new AdminModel();
        $this->view = new View();
    }

    /**
     * @return array
     */
    public function list(): array
    {
        $users = $this->model->getList();

        if (empty($users)){
            Response::error(404, 'No appropriate data found in database');
        }

        return $users;
    }

    /**
     * @param Request $request
     * @param $params
     * @return mixed
     */
    public function get(Request $request, $params): mixed
    {
        $id = $params['id'];
        $info = $this->model->getById($id);

        if (!$info) {
            Response::error(404, 'No appropriate data found in database');
        }

        return $info;
    }

    /**
     * @throws ContainerException
     * @throws ContainerNotFoundException
     */
    public function delete(Request $request, $params):void
    {
        $id = $params['id'];

        if (!$this->model->getById($id)) {
            Response::error(404, 'No appropriate data found in database');
        }

        $this->model->deleteById($id);
        Response::status(204);

        if ((int) $id == $_SESSION['user']['id']) {
            App::get(UserController::class)->logout();
        }
    }

    /**
     * @param Request $request
     * @param $params
     * @return void
     */
    public function update(Request $request, $params): void
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

        $user = $this->model->getByEmail($updateInfo['email']);

        if ($user && $user['id'] !== (int) $id) {
            Response::error(422, 'Such email is already in use');
        }

        if (empty($updateInfo['email']) || empty($updateInfo['password'])) {
            Response::error(400, 'Main fields are not filled in');
        }

        $updatedInfo = $this->model->updateById($id, $updateInfo);

        if (empty($updatedInfo)) {
            Response::error(404, 'No appropriate data found in database');
        }

        if ((int) $id == $_SESSION['user']['id']) {
            $this->updateSessionParams($updatedInfo);
        }

        Response::status(204);
    }

    /**
     * @param array $user
     * @return void
     */
    private function updateSessionParams (array $user): void
    {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'admin' => $user['admin'],
        ];
    }

}

