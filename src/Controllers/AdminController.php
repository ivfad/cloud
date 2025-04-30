<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\UserModel;
use Core\App;
use Core\Exceptions\TransactionException;
use Core\Foundation\Controller;
use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;
use Core\Foundation\View;
use Core\Helpers\Renderable;
use Psr\Container\ContainerExceptionInterface;


class AdminController extends Controller
{
    private UserModel $userModel;
    private FileModel $fileModel;

    function __construct()
    {
        parent::__construct();
        $this->view = new View();

        try {
            $this->userModel = App::get(UserModel::class);
            $this->fileModel = App::get(FileModel::class);
        } catch (ContainerExceptionInterface $e) {
            Response::error(500, $e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function list(): array
    {
        $users = $this->userModel->getUsersListExpanded();

        if (empty($users)) {
            Response::error(404, 'No appropriate data found in database');
        }

        return $users;
    }

    /**
     * @param Request $request
     * @param $params
     * @return void
     * @throws ContainerExceptionInterface
     */
    public function delete(Request $request, $params): void
    {
        $id = $params['id'];
        if (!$this->userModel->getUserById($id)) {
            Response::error(404, 'No appropriate data found in database');
        }

        try {
            if (!$this->userModel->beginTransaction()) {
                throw new TransactionException('DB transaction start failed');
            }
            $filesOwned = $this->fileModel->deleteUserContent($id);
            $this->userModel->deleteUserById($id);
            if (!$this->userModel->commit()) {
                throw new TransactionException('DB transaction commit failed');
            }
        } catch (TransactionException $e) {
            $this->userModel->rollBack();
            Response::error(500, $e->getMessage());
        }
        foreach ($filesOwned as $file) {
            unlink(FILES_PATH . $file['unique_name']);
        }
        Response::status(204);

        if ((int)$id == $_SESSION['user']['id']) {
            App::get(UserController::class)->logout();
        }
    }

    /**
     * @param Request $request
     * @param $params
     * @return mixed
     */
    public function get(Request $request, $params): mixed
    {
        $id = $params['id'];
        $info = $this->userModel->getUserByIdExpanded($id);

        if (!$info) {
            Response::error(404, 'No appropriate data found in database');
        }

        return $info;
    }

    /**
     * @param Request $request
     * @param $params
     * @return void
     */
    public function update(Request $request, $params): void
    {
        $id = $params['id'];

        if (!$this->userModel->getUserById($id)) {
            Response::error('404', 'No appropriate data found in database');
        }

        $updateInfo = [
            'name' => empty($request->post()['name']) ? 'User' : $request->post()['name'],
            'email' => empty($request->post()['email']) ? null : $request->post()['email'],
            'admin' => $request->post()['admin'] ?? 0,
            'age' => empty($request->post()['age']) ? null : $request->post()['age'],
            'gender' => $request->post()['gender'] ?? null,
            'password' => $request->post()['password'] ?? null,
        ];

        if (empty($updateInfo['email']) || empty($updateInfo['password'])) {
            Response::error(400, 'Main fields are not filled in');
        }

        $user = $this->userModel->getUserByEmail($updateInfo['email']);

        if ($user && $user['id'] !== (int)$id) {
            Response::error(422, 'Such email is already in use');
        }
        $updatedInfo = $this->userModel->updateById($id, $updateInfo);

        if (empty($updatedInfo)) {
            Response::error(404, 'No appropriate data found in database');
        }

        if ((int)$id == $_SESSION['user']['id']) {
            $this->updateSessionParams($updatedInfo);
        }

        Response::status(204);
    }

    /**
     * @param array $user
     * @return void
     */
    private function updateSessionParams(array $user): void
    {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'admin' => $user['admin'],
        ];
    }

    /**
     * @return Renderable
     */
    public function updateView(): Renderable
    {
        $this->view->setTemplate('update.admin.view.php');
        $params = [
            'title' => "Update user's info",
            'buttonText' => 'Update',
        ];

        return $this->view->render($params);
    }

}

