<?php

namespace Core\Interfaces;


use Core\Classes\DBConfiguration;

interface IDB
{
    public function __construct(DBConfiguration $DBConfig);
    public function connect();
    public function close();
    public function resultByID(array $recordID,string $table, string $id_field = 'id');
    public function results(array $fields,array $conditions,string $table);
    public function insertRecord(array $recordData,string $table, string $id_field = 'id'): string;
    public function updateRecord(string|int $recordID,array $recordData, string $table, string $id_field = 'id'): string;
    public function deleteRecord(string|int $recordID,string $table, string $id_field = 'id');
    public function deleteManyRecords(array $conditions,string $table);

}