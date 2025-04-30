<?php

namespace Core\Foundation;

use Core\App;
use Core\Database\Database;
use Core\Helpers\IModel;
use Psr\Container\ContainerExceptionInterface;

class Model implements IModel
{
    protected Database $db;

    /**
     * @throws ContainerExceptionInterface
     */
    public function __construct()
    {
        $this->db = App::get(Database::class);
    }

    /**
     * @param string $parameter
     * @param string|int $value
     * @param string $table
     * @param array $columnsList
     * @return Database
     */
    private function get(string $parameter, string|int $value, string $table, array $columnsList = []): Database
    {
        $columns = $this->escapeColumns($columnsList);
        $tableName = $this->escapeIdentifier($table);
        $escapedParameter = $this->escapeIdentifier($parameter);

        $info = $this->db->query(
            "SELECT {$columns} FROM {$tableName} WHERE {$escapedParameter}=:value",
            [':value' => $value]
        );

        return $info;
    }

    /**
     * @param array $data
     * @param string $table
     * @param array $columnsList
     * @return Database
     */
    private function getByParamsList(array $data, string $table, array $columnsList = []): Database
    {
        $columns = $this->escapeColumns($columnsList);
        $tableName = $this->escapeIdentifier($table);

        $condition = $this->prepareConditions($data);
        $bindParams = $this->prepareBindParams($data);

        $info = $this->db->query(
            "SELECT {$columns} FROM {$tableName} WHERE $condition",
            $bindParams
        );

        return $info;
    }

    /**
     * @param string $parameter
     * @param string|int $value
     * @param string $table
     * @param array $columnsList
     * @return array|false
     */
    public function getBy(string $parameter, string|int $value, string $table, array $columnsList = []): array|false
    {
        return $this->get($parameter, $value, $table, $columnsList)->get();
    }

    /**
     * @param string $parameter
     * @param string|int $value
     * @param string $table
     * @param array $columnsList
     * @return mixed
     */
    public function getOneBy(string $parameter, string|int $value, string $table, array $columnsList = []): mixed
    {
        return $this->get($parameter, $value, $table, $columnsList)->find();
    }

    /**
     * @param array $data
     * @param string $table
     * @param array $columnsList
     * @return array|false
     */
    public function getBySeveral(array $data, string $table, array $columnsList = []): array|false
    {
        return $this->getByParamsList($data, $table, $columnsList)->get();
    }

    /**
     * @param array $data
     * @param string $table
     * @param array $columnsList
     * @return mixed
     */
    public function getOneBySeveral(array $data, string $table, array $columnsList = []): mixed
    {
        return $this->getByParamsList($data, $table, $columnsList)->find();
    }

    /**
     * @param array $data
     * @return string
     */
    private function prepareConditions(array $data): string
    {
        $conditionsList = [];

        foreach ($data as $field => $value) {
            if ($value === null) {
                $conditionsList[] = "`{$field}` IS NULL";
                continue;
            }
            $conditionsList[] = "`{$field}` = :{$field}";
        }

        return implode(' AND ', $conditionsList);
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareBindParams(array $data): array
    {
        $bindParams = [];

        foreach ($data as $field => $value) {
            if ($value === null) {
                continue;
            }
            $bindParams[":{$field}"] = $value;
        }

        return $bindParams;
    }

    /**
     * @param int $id
     * @param string $table
     * @param array $columnsList
     * @return mixed
     */
    public function getOneById(int $id, string $table, array $columnsList = []): mixed
    {
        return $this->getOneBy('id', $id, $table, $columnsList);
    }

    /**
     * @param string $table
     * @param array $columnsList
     * @return array|false
     */
    public function getList(string $table, array $columnsList = []): array|false
    {
        $columns = $this->escapeColumns($columnsList);
        $tableName = $this->escapeIdentifier($table);

        $list = $this->db->query("SELECT {$columns} FROM {$tableName} ")->get();

        return $list;
    }

    /**
     * @param string $parameter
     * @param string|int $value
     * @param string $table
     * @return void
     */
    public function deleteBy(string $parameter, string|int $value, string $table): void
    {
        $tableName = $this->escapeIdentifier($table);
        $escapedParameter = $this->escapeIdentifier($parameter);

        $this->db->query(
            "DELETE FROM {$tableName} WHERE {$escapedParameter}=:value",
            [':value' => $value]
        )->get();
    }

    /**
     * @param array $parameters
     * @param string $table
     * @return void
     */
    public function insert(array $parameters, string $table): void
    {
        $tableName = $this->escapeIdentifier($table);
        $bindParams = $this->prepareBindParams($parameters);

        $keys = array_keys($bindParams);
        $valuesList = implode(', ', array_keys($bindParams));;

        $columns = array_map(function ($key) {
            return ltrim($key, ':');
        }, $keys);
        $columns = implode(', ', $columns);

        $this->db->query("INSERT INTO {$tableName}({$columns}) VALUES({$valuesList})", $bindParams)->get();
    }

    /**
     * @param $table
     * @return bool
     */
    public function hasEntries($table): bool
    {
        return (bool)$this->db->query("SELECT COUNT(*) FROM {$table}")->column();
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function escapeIdentifier(string $identifier): string
    {
        return "`{$identifier}`";
    }

    /**
     * @param array $columnsList
     * @return string
     */
    protected function escapeColumns(array $columnsList = []): string
    {
        if (empty($columnsList) || $columnsList[0] === '*') {
            $columns = '*';
        } else {
            $columns = implode(', ', array_map([$this, 'escapeIdentifier'], $columnsList));
        }

        return $columns;
    }

    public function isSuccess(): bool
    {
        return $this->db->rowCount() === 1;
    }

    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->db->commit();
    }

    public function rollback(): bool
    {
        return $this->db->rollback();
    }

}