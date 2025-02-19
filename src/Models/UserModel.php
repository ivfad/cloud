<?php

namespace App\Models;

use Core\Foundation\Model;

class UserModel extends Model
{
    /**
     * @return array
     */
    public function getList(): array
    {
        $list = $this->db->query('Select `name`, `age`, `gender` from `user`')->get();

        return $list;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id): mixed
    {
        $info = $this->db->query('Select `name`, `age`, `gender` from `user` WHERE `id` = :id', [
            ':id' => $id,
        ])->find();

        return $info;
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

    /**
     * @param $name
     * @param $email
     * @param $password
     * @param $age
     * @param $gender
     * @param $initialEmail
     * @return mixed
     */

    public function updateInfo($name, $email, $password, $age, $gender, $initialEmail): mixed
    {
        $this->db->query(query: 'UPDATE `user` 
            SET 
                name = :name, 
                email = :email,
                password = :password,
                age = :age,
                gender = :gender 
            WHERE email = :initialEmail',
            params: [
                ':initialEmail' => $initialEmail,
                ':name' => $name,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':age' => $age,
                ':gender' => $gender
        ]);

        return $this->getByEmail($email);
    }
}