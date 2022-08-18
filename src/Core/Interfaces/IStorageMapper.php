<?php

namespace Core\Interfaces;



interface IStorageMapper
{
    /**
     * @param string|int $recordID
     * @param string $table
     * @param string $id_field
     *
     * @return array<mixed>|null
     */
    public function resultByID(string|int $recordID, string $table, string $id_field = 'id'): array|null;
    /**
     * @param array<string> $fields
     * @param array<string> $conditions
     * @param string $table
     *
     * @return array<mixed>
     */
    public function results(array $fields, array $conditions, string $table): array;
    /**
     * @param array<mixed> $recordData
     * @param string $table
     * @param string $id_field
     *
     * @return string|int|null
     */
    public function insertRecord(array $recordData, string $table, string $id_field = 'id'): string|int|null;
    /**
     * @param string|int $recordID
     * @param array<mixed> $recordData
     * @param string $table
     * @param string $id_field
     *
     * @return string|int
     */
    public function updateRecord(string|int $recordID, array $recordData, string $table, string $id_field = 'id'): string|int;
    /**
     * @param string|int $recordID
     * @param string $table
     * @param string $id_field
     *
     * @return void
     */
    public function deleteRecord(string|int $recordID, string $table, string $id_field = 'id'): void;
    /**
     * @param array<string|int> $recordIDs
     * @param string $table
     * @param string $id_field
     *
     * @return void
     */
    public function deleteManyRecordsByID(array $recordIDs, string $table, string $id_field = 'id'): void;
    /**
     * @param array<string> $conditions
     * @param string $table
     *
     * @return void
     */
    public function deleteManyRecords(array $conditions, string $table): void;
}
