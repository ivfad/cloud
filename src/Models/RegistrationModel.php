<?php

namespace App\Models;

use Core\Foundation\Model;

class RegistrationModel extends Model
{
    /**
     * @param $email
     * @return mixed
     */
    public function getByEmail($email): mixed
    {
        return $this->db->query('SELECT * FROM `user` where email = :email', [
            ':email' => $email,
        ])->find();
    }

    /**
     * @param $email
     * @param $password
     * @return void
     */
    public function addUser($email, $password): void
    {
        $this->db->query('INSERT INTO `user`(email, password) VALUES(:email, :password)', [
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
        ]);
    }
}