<?php

namespace Core\Helpers;

interface IModel
{
    public function getBy(string $parameter, string|int $value, string $table, array $columnsList = []): array|false;

    public function getOneBy(string $parameter, string|int $value, string $table, array $columnsList = []): mixed;

    public function getBySeveral(array $data, string $table, array $columnsList = []): array|false;

    public function getOneBySeveral(array $data, string $table, array $columnsList = []): mixed;

    public function getOneById(int $id, string $table, array $columnsList = []): mixed;

    public function getList(string $table, array $columnsList = []): array|false;

    public function deleteBy(string $parameter, string|int $value, string $table): void;

    public function insert(array $parameters, string $table): void;

    public function hasEntries($table): bool;

    public function isSuccess(): bool;

}