<?php

namespace App\Models;

use Core\Foundation\Model;

class AdminModel extends Model
{
    /**
     * @return array
     */
    public function getList(): array
    {
        $list = $this->db->query('SELECT * from `user`')->get();

        return $list;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id): mixed
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
    public function deleteById($id): void
    {
        $this->db->query('DELETE from `user` WHERE `id` = :id', [
            ':id' => $id,
        ]);
    }

    /**
     * @param $id
     * @param $updateInfo
     * @return mixed
     */
    public function updateById($id, $updateInfo): mixed
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

        return $this->getByEmail($updateInfo['email']);
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getByEmail($email): mixed
    {
        $user = $this->db->query('Select * from `user` WHERE `email` = :email', [
            ':email' => $email,
        ])->find();

        return $user;
    }
}