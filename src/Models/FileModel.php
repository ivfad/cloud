<?php

namespace App\Models;

use Core\App;
use Core\Cache\RedisService;
use Core\Exceptions\TransactionException;
use Core\Foundation\Model;

class FileModel extends Model
{
    private RedisService $redis;

    public function __construct()
    {
        parent::__construct();
        $this->redis = App::get(RedisService::class);
    }

    /**
     * @param string $filename
     * @param string $uniqueName
     * @param int $userID
     * @param int|null $folderID
     * @return mixed
     * @throws TransactionException
     */
    public function add(string $filename, string $uniqueName, int $userID, ?int $folderID): mixed
    {
        try {
            if (!$this->beginTransaction()) {
                throw new TransactionException('DB transaction start failed');
            }

            $data = ['filename' => $filename,
                'unique_name' => $uniqueName,
                'owner_id' => $userID,
                'folder_id' => $folderID
            ];

            $this->insert($data, 'file');

            $fileID = $this->db->lastID();

            $this->addAccess($userID, $fileID, status: 'Owner');

            if (!$this->commit()) {
                throw new TransactionException('DB transaction commit failed');
            }
        } catch (TransactionException) {
            $this->rollback();
            throw new TransactionException('DB transaction failed');
        }

        return $this->getFileByID($fileID, ['id', 'filename', 'folder_id']);
    }

    /**
     * @param int $userID
     * @param int|null $fileID
     * @param int|null $folderID
     * @param string $status
     * @return mixed
     */
    public function addAccess(int $userID, ?int $fileID = null, ?int $folderID = null, string $status = 'User'): mixed
    {
        $data = ['user_id' => $userID,
            'file_id' => $fileID,
            'folder_id' => $folderID,
            'status' => $status
        ];

        $this->insert($data, 'access');

        return $this->getOneById($this->db->lastID(), 'access');
    }

    /**
     * @param string $name
     * @param int $userID
     * @param int|null $parentID
     * @return mixed
     * @throws TransactionException
     */
    public function addFolder(string $name, int $userID, ?int $parentID = null): mixed
    {
        try {
            if (!$this->beginTransaction()) {
                throw new TransactionException('DB transaction start failed');
            }

            $data = ['name' => $name,
                'owner_id' => $userID,
                'parent_id' => $parentID,
            ];

            $this->insert($data, 'folder');
            $folderID = $this->db->lastID();

            $this->addAccess($userID, null, $folderID, status: 'Owner');

            if (!$this->commit()) {
                throw new TransactionException('DB transaction commit failed');
            }
        } catch (TransactionException) {
            $this->rollback();
            throw new TransactionException('DB transaction failed');
        }

        return $this->getFolderByID($folderID, ['id', 'name', 'parent_id']);
    }

    /**
     * @param string $filename
     * @param int $userID
     * @param int|null $folderID
     * @return bool
     */
    public function checkFileExists(string $filename, int $userID, ?int $folderID): bool
    {
        $data = [
            'filename' => $filename,
            'owner_id' => $userID,
            'folder_id' => $folderID
        ];

        return (bool)$this->getOneBySeveral($data, 'file');
    }

    /**
     * @param string $folder
     * @param int $userID
     * @param int|null $parentID
     * @return bool
     */
    public function checkFolderExists(string $folder, int $userID, ?int $parentID): bool
    {
        $data = [
            'name' => $folder,
            'owner_id' => $userID,
            'parent_id' => $parentID
        ];

        return (bool)$this->getOneBySeveral($data, 'folder');
    }

    public function deleteUserContent(int $userID): array
    {
        $filesOwned = $this->getBy('owner_id', $userID, 'file', ['id', 'unique_name']);

        $this->deleteBy('owner_id', $userID, 'folder');
        $this->deleteBy('owner_id', $userID, 'file');
        $this->deleteBy('user_id', $userID, 'access');

        return $filesOwned;
    }

    /**
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function getFileByID(int $id, array $columns = []): mixed
    {
        return $this->getOneById($id, 'file', $columns);
    }

    /**
     * @param int $fileID
     * @return array|false
     */
    public function getFileUsers(int $fileID): array|false
    {
        return $this->getBy('file_id', $fileID, 'access');
    }

    /**
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function getFolderByID(int $id, array $columns = []): mixed
    {
        return $this->getOneById($id, 'folder', $columns);
    }

    /**
     * @param int $folderID
     * @param array $columns
     * @return array|false
     */
    public function getFolderContent(int $folderID, array $columns = []): array|false
    {
        return $this->getBy('folder_id', $folderID, 'file');
    }

    /**
     * @param int $userID
     * @return array|bool
     */
    public function getRootFolderContent(int $userID): array|false
    {
        return $this->db->query(
            query: "SELECT 
                        f.`id`,
                        f.`filename`,
                        '/' as `path`
                    FROM `access` as `a`
                    JOIN `file` as f on `a`.`file_id` = f.`id`
                    WHERE `f`.`folder_id` IS NULL 
                    AND `user_id` = :userID",
            params: [':userID' => $userID])->get();
    }

    /**
     * @param int $userID
     * @return array|false
     */
    public function list(int $userID): array|false
    {
        $list = $this->db->query(
            query: "SELECT
                        `f`.`id`,
                        `f`.folder_id,
                        `f`.`filename`,
                        `a`.`status`
                    FROM
                        `access` as `a`
                    JOIN
                            `file` as `f` on `file_id` = `f`.`id`
                    WHERE
                        `a`.`user_id` = :userID
                    AND
                        `a`.`file_id` IS NOT NULL",
            params: [':userID' => $userID])->get();

        return $list;
    }

    /**
     * @param int $id
     * @return void
     */
    public function removeAccess(int $id): void
    {
        $this->deleteBy('id', $id, 'access');
    }

    /**
     * @param int $id
     * @return void
     */
    public function removeFile(int $id): void
    {
        $this->deleteBy('id', $id, 'file');
    }

    /**
     * @param int $id
     * @return array
     * @throws TransactionException
     */
    public function removeFolder(int $id): array
    {
        $this->invalidateAssociatedCache($id);

        try {
            if (!$this->beginTransaction()) {
                throw new TransactionException('DB transaction start failed');
            }

            $filesList = $this->collectAllFiles($id);

            foreach ($filesList as $fileID => $fileName) {
                $this->removeFile($fileID);
            }

            $this->deleteBy('id', $id, 'folder');

            if (!$this->commit()) {
                throw new TransactionException('DB transaction commit failed');
            }
        } catch (TransactionException) {
            $this->rollback();
            throw new TransactionException('DB transaction failed');
        }

        return $filesList;
    }

    /**
     * @param string $filename
     * @param int $id
     * @return mixed
     */
    public function rename(string $filename, int $id): mixed
    {
        $this->db->query(query: 'UPDATE `file`
            SET
                `filename` = :filename
            WHERE `id` = :id',
            params: [
                ':id' => $id,
                ':filename' => $filename,
            ]);

        return $this->getFileByID($id, ['id', 'filename']);
    }

    /**
     * @param string $name
     * @param int $id
     * @return mixed
     */
    public function renameFolder(string $name, int $id): mixed
    {
        $this->db->query(query: 'UPDATE `folder`
            SET
                `name` = :name
            WHERE `id` = :id',
            params: [
                ':id' => $id,
                ':name' => $name,
            ]);

        $this->invalidateAssociatedCache($id);

        return $this->getFolderByID($id, ['id', 'name']);
    }

    /**
     * @param int $folderID
     * @return string
     */
    public function getFullPath(int $folderID): string
    {
        if ($this->getCachedPath($folderID)) {
            return $this->getCachedPath($folderID);
        }

        $sql = "
            WITH RECURSIVE folder_path AS (
                SELECT id, name, parent_id
                FROM folder
                WHERE id = :folder_id
    
                UNION ALL
    
                SELECT f.id, f.name, f.parent_id
                FROM folder f
                JOIN folder_path fp ON f.id = fp.parent_id
            )
            SELECT GROUP_CONCAT(name ORDER BY parent_id IS NULL DESC SEPARATOR '/') AS full_path
            FROM folder_path
        ";

        $path = $this->db->query($sql, params: [':folder_id' => $folderID])->find();

        $fullPath = $path ? '/' . $path['full_path'] : '/';

        $this->setCachedPath($folderID, $fullPath);

        return $fullPath;
    }

    /**
     * @param int $folderID
     * @return array|false
     */
    public function collectFoldersList(int $folderID): array|false
    {
        return $this->getBy('parent_id', $folderID, 'folder');
    }

    /**
     * DFS-algorithm implementation. Recursively collects a list of all files in current and all child folders
     * @param int $folderID
     * @return array
     */
    public function collectAllFiles(int $folderID): array
    {
        $allFiles = [];

        $dfs = function ($folderID) use (&$dfs, &$allFiles) {
            $files = $this->getFolderContent($folderID);

            foreach ($files as $file) {
                $allFiles[$file['id']] = $file['unique_name'];
            }

            $subFolders = $this->collectFoldersList($folderID);
            foreach ($subFolders as $subFolder) {
                $dfs($subFolder['id']);
            }
        };

        $dfs($folderID);

        return $allFiles;
    }

    /**
     * @param int $folderID
     * @return string|null
     */
    private function getCachedPath(int $folderID): ?string
    {
        $cacheKey = "path:folder:$folderID";

        return $this->redis->get($cacheKey);
    }

    /**
     * @param int $folderID
     * @param string $path
     * @return void
     */
    private function setCachedPath(int $folderID, string $path): void
    {
        $cacheKey = "path:folder:$folderID";
        $this->redis->setEx($cacheKey, 600, $path);
    }

    /**
     * @param int $folderID
     * @return void
     */
    private function invalidateAssociatedCache(int $folderID): void
    {
        $queue = [$folderID];

        while (!empty($queue)) {
            $currentFolderID = array_shift($queue);

            $cacheKey = "path:folder:$currentFolderID";
            $this->redis->delete($cacheKey);

            $childFolders = $this->collectFoldersList($currentFolderID);
            foreach ($childFolders as $childFolder) {
                $queue[] = $childFolder['id'];
            }
        }
    }
}