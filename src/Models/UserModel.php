<?php

namespace App\Models;

use Core\App;
use Core\Database;
use Core\Exceptions\ContainerException;
use Core\Exceptions\ContainerNotFoundException;
use Core\Model;
use Core\Response;

class UserModel extends Model
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
        $list = $this->db->query('Select `name`, `age`, `gender` from `user`')->get();

        return $list;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUserInfoById($id)
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
    public function getUserByEmail($email): mixed
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

    public function updateUserInfo($name, $email, $password, $age, $gender, $initialEmail): mixed
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

        return $this->getUserByEmail($email);
    }
}