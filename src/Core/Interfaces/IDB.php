<?php

namespace Core\Interfaces;

use Core\Classes\DBConfiguration;

interface IDB
{
    /**
     * @param DBConfiguration $DBConfig
     */
    public function __construct(DBConfiguration $DBConfig);
    /**
     * @return mixed
     */
    public function connect(): mixed;
    /**
     * @return void
     */
    public function close(): void;
    /**
     * @param array<string|int> $recordID
     * @param string $table
     * @param string $id_field
     *
     * @return mixed
     */
    public function resultByID(array $recordID, string $table, string $id_field = 'id'): mixed;
    /**
     * @param array<string> $fields
     * @param array<string> $conditions
     * @param string $table
     *
     * @return mixed
     */
    public function results(array $fields, array $conditions, string $table): mixed;
    /**
     * @param array<mixed> $recordData
     * @param string $table
     * @param string $id_field
     *
     * @return string
     */
    public function insertRecord(array $recordData, string $table, string $id_field = 'id'): string;
    /**
     * @param string|int $recordID
     * @param array<mixed> $recordData
     * @param string $table
     * @param string $id_field
     *
     * @return string
     */
    public function updateRecord(string|int $recordID, array $recordData, string $table, string $id_field = 'id'): string;
    /**
     * @param string|int $recordID
     * @param string $table
     * @param string $id_field
     *
     * @return void
     */
    public function deleteRecord(string|int $recordID, string $table, string $id_field = 'id'): void;
    /**
     * @param array<string> $conditions
     * @param string $table
     *
     * @return void
     */
    public function deleteManyRecords(array $conditions, string $table): void;
}
