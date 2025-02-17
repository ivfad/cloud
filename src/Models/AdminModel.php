<?php

namespace App\Models;

use Core\App;
use Core\Database;
use Core\Exceptions\ContainerException;
use Core\Exceptions\ContainerNotFoundException;
use Core\Model;

class AdminModel extends Model
{
    private Database $db;

    /**
     * @throws ContainerException
     * @throws ContainerNotFoundException
     */
    public function __construct()
    {
//        $this->db = App::getContainer()->get(Database::class);
        $this->db = App::get(Database::class);
    }

    /**
     * @return bool|array
     */
    public function getUsersList(): bool|array
    {
        $list = $this->db->query('SELECT `id`, `name`, `email`, `admin`, `age`, `gender` from `user`')->get();

        return $list;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUserInfoById($id): mixed
    {
        $info = $this->db->query('SELECT * from `user` WHERE `id` = :id', [
            ':id' => $id,
        ])->find();

        return $info;
    }

    /**
     * @param $id
     * @return void
     */
    public function deleteUserById($id): void
    {
        $this->db->query('DELETE from `user` WHERE `id` = :id', [
            ':id' => $id,
        ]);
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email): mixed
    {
        $user = $this->db->query('Select * from `user` WHERE `email` = :email', [
            ':email' => $email,
        ])->find();

        return $user;
    }

    /**
     * @param $id
     * @param $updateInfo
     * @return mixed
     */
    public function updateUserInfoById($id, $updateInfo): mixed
    {
        $this->db->query('UPDATE `user` SET `name` = :name, `email` = :email, `admin` = :admin, `age` = :age, `gender` = :gender, `password` = :password
            WHERE `id` = :id', [
            ':id' => $id,
            ':name' => $updateInfo['name'],
            ':email' => $updateInfo['email'],
            ':admin' => $updateInfo['admin'],
            ':age' => $updateInfo['age'],
            ':gender' => $updateInfo['gender'],
            ':password' => password_hash($updateInfo['password'], PASSWORD_BCRYPT)
        ]);

        return $this->getUserByEmail($updateInfo['email']);
    }
}