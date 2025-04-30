<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\UserModel;
use Core\App;
use Core\Exceptions\TransactionException;
use Core\Exceptions\UploadException;
use Core\Foundation\Controller;
use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;
use Core\Foundation\View;
use Psr\Container\ContainerExceptionInterface;

class FileController extends Controller

{
    private UserModel $userModel;

    function __construct()
    {
        parent::__construct();
        $this->model = new FileModel();
        $this->view = new View();
        try {
            $this->userModel = App::get(UserModel::class);
        } catch (ContainerExceptionInterface $e) {
            Response::error(500, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $params
     * @return void
     */
    public function accessDelete(Request $request, $params): void
    {
        $fileID = $params['id'];
        $userID = $params['user_id'];
        $ownerID = $_SESSION['user']['id'];

        $file = $this->checkFile($fileID, $ownerID, onlyOwner: true);

        if (!$this->userModel->getUserById($userID)) {
            Response::error(400, 'No such user');
        }

        if ($userID == $ownerID) {
            Response::error(403, 'You cannot delete your own access');
        }

        if (!$this->checkAccess($fileID, $userID)) {
            Response::error(422, 'User has no access to file');
        }

        $accessID = $this->model->getOneBySeveral(['user_id' => $userID, 'file_id' => $fileID], 'access')['id'];

        $this->model->removeAccess($accessID);

        Response::status(204);
    }

    /**
     * @param Request $request
     * @param $params
     * @return array
     */
    public function accessList(Request $request, $params): array
    {
        $fileID = $params['id'];
        $userID = $_SESSION['user']['id'];

        $file = $this->checkFile($fileID, $userID);

        return $this->model->getFileUsers($fileID);
    }

    /**
     * @param Request $request
     * @param $params
     * @return mixed
     */
    public function accessShare(Request $request, $params): mixed
    {
        $fileID = $params['id'];
        $newUserID = $params['user_id'];
        $ownerID = $_SESSION['user']['id'];

        $file = $this->checkFile($fileID, $ownerID, onlyOwner: true);

        if (!$this->userModel->getUserById($newUserID)) {
            Response::error(400, 'No such user');
        }

        if ($this->checkAccess($fileID, $newUserID)) {
            Response::error(422, 'User already has access to file');
        }

        return $this->model->addAccess($newUserID, $fileID);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request): mixed
    {
        $file = $request->files()['file'] ?? Response::error(422, 'No file uploaded');

        $filename = basename($file['name']);

        $this->checkName($filename);
        $this->checkSize($file, 2);

        $userID = $_SESSION['user']['id'];
        $folderID = $request->post()['folder_id'];

        if ($this->model->checkFileExists($filename, $userID, $folderID)) {
            Response::error(422, 'File already exists');
        }

        if ($folderID !== null) {
            $this->checkFolder($folderID, $userID, onlyOwner: true, isParent: true);
        }

        if ($file['error'] !== 0) {
            try {
                $this->handleErrors($file);
            } catch (UploadException $e) {
                Response::error(500, $e->getMessage());
            }
        }

        $extension = strrchr($filename, '.');

        $tmpFile = $file['tmp_name'];

        $newName = md5(microtime() . $tmpFile) . $extension;
        $path = FILES_PATH . $newName;

        try {
            if (move_uploaded_file($tmpFile, $path)) {
                $fileInstance = $this->model->add($filename, $newName, $userID, $folderID);
                $this->addFullPath($fileInstance, $folderID);
            } else {
                throw new UploadException('Upload error');
            }
        } catch (UploadException $e) {
            Response::error(500, $e->getMessage());
        } catch (TransactionException $e) {
            unlink($path);
            Response::error(500, $e->getMessage());
        }

        return $fileInstance;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function addFolder(Request $request): mixed
    {
        $folder = $request->post()['folder'] ?? Response::error(500, 'Empty name of the folder');
        $parentID = $request->post()['parent_folder'];
        $userID = (int)$_SESSION['user']['id'];

        if ($this->model->checkFolderExists($folder, $userID, $parentID)) {
            Response::error(422, 'Folder already exists');
        }

        if ($parentID !== null) {
            $this->checkFolder($parentID, $userID, onlyOwner: true, isParent: true);
        }

        try {
            $folderInstance = $this->model->addFolder($folder, $userID, $parentID);
        } catch (TransactionException $e) {
            Response::error(500, $e->getMessage());
        }

        return $folderInstance;
    }

    /**
     * @param $entry
     * @param $folderID
     * @return void
     */
    private function addFullPath(&$entry, $folderID): void
    {
        $entry['path'] = $folderID ? $this->model->getFullPath($folderID) : '/';
    }

    /**
     * @param int|null $instanceID
     * @param int $userID
     * @param bool $isFolder
     * @param bool $onlyOwner
     * @return bool
     */
    private function checkAccess(?int $instanceID, int $userID, bool $isFolder = false, bool $onlyOwner = false): bool
    {
        $conditions = [
            'user_id' => $userID,
        ];

        if ($onlyOwner) {
            $conditions['status'] = 'Owner';
        }

        if ($isFolder) {
            $conditions['folder_id'] = $instanceID;
        } else {
            $conditions ['file_id'] = $instanceID;
        }

        $fileInstance = $this->model->getOneBySeveral($conditions, 'access');

        return !empty($fileInstance);
    }

    private function checkFile($fileID, $userID, $onlyOwner = false): mixed
    {

        $file = $this->model->getFileById($fileID);

        if (!$file) {
            Response::error(400, 'No such file');
        }
        if (!$this->checkAccess($fileID, $userID, onlyOwner: $onlyOwner)) {
            Response::error(403, 'Access denied');
        }

        return $file;
    }

    private function checkFolder($folderID, $userID, $onlyOwner = false, $isParent = false): mixed
    {
        $folder = $this->model->getFolderByID($folderID);

        if (!$folder) {
            $message = $isParent ? 'No such folder' : 'No such parent folder';
            Response::error(400, $message);
        }

        if (!$this->checkAccess($folderID, $userID, isFolder: true, onlyOwner: $onlyOwner)) {
            Response::error(403, 'Access denied');
        }

        return $folder;
    }

    /**
     * @param $name
     * @return void
     */
    private function checkName($name): void
    {
        $forbiddenChars = '/[\/:*?"<>|]/';

        if (empty($name) || preg_match($forbiddenChars, $name) || (strlen($name) > 255)) {
            Response::error(422, 'File name is invalid');
        }
    }

    /**
     * @param $file
     * @param int $maxMB
     * @return void
     */
    private function checkSize($file, int $maxMB = 2): void
    {
        if ($file['size'] > ($maxMB * 1024 * 1024)) {
            Response::error(413, "File must be less than {$maxMB}MB");
        }
    }

    /**
     * @param Request $request
     * @param $params
     * @return false|int
     */
    public function get(Request $request, $params): false|int
    {

        $fileID = $params['id'];
        $userID = $_SESSION['user']['id'];

        $file = $this->checkFile($fileID, $userID);

        $filename = $file['filename'];
        $uniqueName = $file['unique_name'];
        $file = FILES_PATH . $uniqueName;

        Response::sendFile(200, $file, $filename);

        return readfile(FILES_PATH . $filename);
    }

    /**
     * @param Request $request
     * @param $params
     * @return array|bool
     */
    public function getFolder(Request $request, $params): array|bool
    {
        $folderID = (int)$params['id'];

        $userID = $_SESSION['user']['id'];

        if ($folderID === 0) {

            return $this->model->getRootFolderContent($userID);
        } else {
            $this->checkFolder($folderID, $userID, onlyOwner: true);
            $list = $this->model->getFolderContent($folderID, ['id', 'filename', 'folder_id']);

            foreach ($list as &$row) {
                $folderID = $row['folder_id'];
                $this->addFullPath($row, $folderID);
            }

            return $list;
        }
    }

    /**
     * @param $file
     * @return void
     * @throws UploadException
     */
    private function handleErrors($file): void
    {
        switch ($file['error']) {
            case 1:
            case 2:
                Response::error(413, 'File must be less than 2MB');
                break;
            case 3:
            case 4:
                Response::error(400, 'No file or file was only partially uploaded. Try again!');
                break;
            default:
                throw new UploadException("File upload exception. UploadError {$file['error']}");
        }
    }

    public function list(Request $request): array|bool
    {
        $userID = $_SESSION['user']['id'];

        $list = $this->model->list($userID);

        foreach ($list as &$row) {
            $folderID = $row['folder_id'];
            $this->addFullPath($row, $folderID);
        }

        return $list;
    }

    /**
     * @param Request $request
     * @param $params
     * @return mixed
     */
    public function rename(Request $request, $params): mixed
    {
        $fileID = $params['id'];
        $filename = $request->post()['filename'];

        $this->checkName($filename);

        $userID = $_SESSION['user']['id'];

        $file = $this->checkFile($fileID, $userID, onlyOwner: true);

        $folderID = $file['folder_id'];

        if ($this->model->checkFileExists($filename, $userID, $folderID)) {
            Response::error(422, 'File already exists');
        }

        $renamedFile = $this->model->rename($filename, $fileID);
        $this->addFullPath($renamedFile, $folderID);

        return $renamedFile;
    }

    /**
     * @param Request $request
     * @param $params
     * @return mixed
     */
    public function renameFolder(Request $request, $params): mixed
    {
        $folderID = $params['id'];

        $newName = $request->post()['folder_name'];
        $this->checkName($newName);

        $userID = $_SESSION['user']['id'];
        $folder = $this->checkFolder($folderID, $userID, onlyOwner: true);

        if ($this->model->checkFolderExists($newName, $userID, $folder['parent_id'])) {
            Response::error(422, 'Folder already exists');
        }

        return $this->model->renameFolder($newName, $folder['id']);
    }

    public function remove(Request $request, $params): void
    {
        $fileID = $params['id'];
        $userID = $_SESSION['user']['id'];

        $file = $this->checkFile($fileID, $userID, onlyOwner: true);

        $this->model->removeFile($fileID);

        if (!$this->model->isSuccess()) {
            Response::error(500, 'Server failed to remove the file');
        }

        unlink(FILES_PATH . $file['unique_name']);
        Response::status(204);
    }

    /**
     * @param Request $request
     * @param $params
     * @return void
     */
    public function removeFolder(Request $request, $params): void
    {
        $folderID = $params['id'];

        $userID = $_SESSION['user']['id'];

        $folder = $this->checkFolder($folderID, $userID, onlyOwner: true);

        try {
            $deletedFilesList = $this->model->removeFolder($folderID);
        } catch (TransactionException $e) {
            Response::error(500, 'Server failed to remove directory');
        }

        foreach ($deletedFilesList as $fileName) {
            unlink(FILES_PATH . $fileName);
        }

        Response::status(204);
    }
}