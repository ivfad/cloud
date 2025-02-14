<?php

namespace App\Models;

use Core\App;
use Core\Database;
use Core\Model;
use Core\Response;

class UserModel extends Model
{
    private Database $db;

    /**
     * @throws \Core\Exceptions\ContainerException
     * @throws \Core\Exceptions\ContainerNotFoundException
     */
    public function __construct()
    {
        $this->db = App::getContainer()->get(Database::class);
    }

    /**
     * @return bool|array
     */
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
        $user = $this->getUserByEmail($initialEmail);

        if (!$user) {
            Response::error(400, 'No such user, please re-login');
        }

        if (empty($email) || empty($password)) {
            Response::error(400, 'Main fields are not filled in');
        }

        if ($email != $initialEmail && $this->getUserByEmail($email)) {
            Response::error(422, 'Such email is already in use');
        }

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