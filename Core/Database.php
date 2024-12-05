<?php

namespace Core;

use Config;
use PDO;
use PDOStatement;

class Database
{
    /**
     * Database of the application. Uses singleton pattern, implements methods for connecting and executing DB-queries
     */

    use SingletonTrait;
    protected PDO $connection;
    protected PDOStatement $statement;

    /**
     * @param Config $config
     * @param string $username
     * @param string $password
     * @return PDO
     */
    public function connect(Config $config, string $username = 'root', string $password = ''): PDO
    {
        $dsn = 'mysql:' . http_build_query($config, arg_separator: ';');

        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $this->connection;
    }

    /**
     * @param string $query
     * @param array $params
     * @return Database
     */
    public function query(string $query, array $params = []):Database
    {
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);

        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function find(): mixed
    {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function findOrFail(): mixed
    {
        $result = $this->statement->fetch();

//        !$result ? : abort();
        if(! $result) {
            abort();
        }

        return $result;
    }
}