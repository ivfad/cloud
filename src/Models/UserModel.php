<?php

namespace App\Models;

use Core\App;
use Core\Database;
use Core\Model;

class UserModel extends Model
{
    private Database $db;

    public function __construct()
    {
        $this->db = App::getContainer()->get(Database::class);
    }

    public function getUsersList(): bool|array
    {
        $list = $this->db->query('Select `name`, `age`, `gender` from `user`')->get();
        return $list;
    }

    public function getUserInfoById($id)
    {
        $info = $this->db->query('Select `name`, `age`, `gender` from `user` WHERE `id` = :id', [
            ':id' => $id,
        ])->find();

        return $info;
    }

    public function getUserByEmail($email)
    {
        $user = $this->db->query('Select * from `user` WHERE `email` = :email', [
            ':email' => $email,
        ])->find();

        return $user;
    }

    public function updateUserInfo($name, $age, $gender, $email)
    {
        $user = $this->db->query('Update `user` SET `name` = :name, `age` = :age, `gender` = :gender
            WHERE `email` = :email', [
            ':name' => $name,
            ':age' => $age,
            ':gender' => $gender,
            ':email' => $email
        ]);
//        ])->find();
    }
}