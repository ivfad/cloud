<?php

namespace App\Models;

use Core\Exceptions\TransactionException;
use Core\Foundation\Model;

class UserModel extends Model
{
    /**
     * @return array|false
     */
    public function getUsersList(): array|false
    {
        return $this->getList('user', ['id', 'name', 'email', 'age', 'gender']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUserById($id): mixed
    {
        return $this->getOneById($id, 'user', ['id', 'name', 'email', 'age', 'gender', 'admin']);
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email): mixed
    {
        return $this->getOneBy('email', $email, 'user');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUserByIdExpanded($id): mixed
    {
        return $this->getOneById($id, 'user');
    }

    /**
     * @return array|false
     */
    public function getUsersListExpanded(): array|false
    {
        return $this->getList('user');
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteUserById(int $id): void
    {
        $this->deleteBy('id', $id, 'user');
    }

    /**
     * @param int $id
     * @param array $updateInfo ['name' => 'name', 'email' => 'email', 'admin' => '0|1', 'password' => 'password', 'age' => '123', 'gender' => 'M|F']
     * @return mixed
     */
    public function updateById(int $id, array $updateInfo): mixed
    {

        $this->db->query(query: 'UPDATE `user`
            SET 
                `name` = :name,
                `email` = :email,
                `admin` = :admin,
                `password` = :password,
                `age` = :age,
                `gender` = :gender
            WHERE `id` = :id',
            params: [
                ':id' => $id,
                ':name' => $updateInfo['name'],
                ':email' => $updateInfo['email'],
                ':admin' => $updateInfo['admin'],
                ':age' => $updateInfo['age'],
                ':gender' => $updateInfo['gender'],
                ':password' => password_hash($updateInfo['password'], PASSWORD_BCRYPT)
            ]);

        return $this->getUserById($id);
    }

    /**
     * @param string $email
     * @param string $password
     * @param int $admin
     * @return void
     */
    public function addUser(string $email, string $password, int $admin = 0): void
    {
        $this->insert(['email' => $email, 'password' => $password, 'admin' => $admin], 'user');
    }
}