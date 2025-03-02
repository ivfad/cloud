<?php

namespace Core\Database;

use Config\DbConfig;
use Core\Foundation\Http\Response;
use Core\Helpers\SingletonTrait;
use PDO;
use PDOException;
use PDOStatement;

class Database
{
    /**
     * Database of the application.
     * Uses singleton pattern, implements methods for connecting and executing DB-queries
     */

    use SingletonTrait;

    protected PDO $connection;
    protected PDOStatement $statement;

    /**
     * Initializes connection to database
     * @param DbConfig $config
     * @param string $username
     * @param string $password
     * @return PDO
     */
    public function connect(DbConfig $config, string $username = 'root', string $password = ''): PDO
    {
        $dsn = 'mysql:' . http_build_query($config, arg_separator: ';');

        $this->connection = new PDO(
            $dsn,
            $username,
            $password,
            [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,]
        );
        return $this->connection;
    }

    /** Executes an SQL query
     * @param string $query
     * @param array $params
     * @return Database
     */
    public function query(string $query, array $params = []): Database
    {
        try {
            $this->statement = $this->connection->prepare($query);
            $this->statement->execute($params);

            return $this;
        } catch (PDOException $e) {
            Response::error(500, "SQL-query error " . $e->getMessage());
        }
    }

    /**
     * Returns an array containing all the results
     * @return array
     */
    public function get(): array
    {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches the next row from a result set
     * @return mixed
     */
    public function find(): mixed
    {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }
}