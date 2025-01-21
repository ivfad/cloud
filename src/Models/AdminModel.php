<?php

namespace App\Models;

use Core\App;
use Core\Database;
use Core\Model;

class AdminModel extends Model
{
    private Database $db;

    public function __construct()
    {
        $this->db = App::getContainer()->get(Database::class);
    }

    public function getUsersList(): bool|array
    {
        $list = $this->db->query('SELECT `id`, `name`, `email`, `admin`, `age`, `gender` from `user`')->get();

        return $list;
    }

    public function getUserInfoById($id): mixed
    {
        $info = $this->db->query('SELECT `id`, `name`, `email`, `admin`, `age`, `gender` from `user` WHERE `id` = :id', [
            ':id' => $id,
        ])->find();

        return $info;
    }

    public function deleteUserById($id): Database
    {
        return $this->db->query('DELETE from `user` WHERE `id` = :id', [
            ':id' => $id,
        ]);
    }

    public function getUserByEmail($email): mixed
    {
        $user = $this->db->query('Select * from `user` WHERE `email` = :email', [
            ':email' => $email,
        ])->find();

        return $user;
    }

    public function updateUserInfoById($id, $updateInfo)
    {
        return $this->db->query('UPDATE `user` SET `name` = :name, `email` = :email, `admin` = :admin, `age` = :age, `gender` = :gender, `password` = :password
            WHERE `id` = :id', [
            ':id' => $id,
            ':name' => $updateInfo['name'],
            ':email' => $updateInfo['email'],
            ':admin' => $updateInfo['admin'],
            ':age' => $updateInfo['age'],
            ':gender' => $updateInfo['gender'],
            ':password' => password_hash($updateInfo['password'], PASSWORD_BCRYPT)
        ]);
    }
}