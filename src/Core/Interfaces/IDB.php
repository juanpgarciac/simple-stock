<?php

namespace Core\Interfaces;


use Core\Classes\DBConfiguration;

interface IDB
{
    public function __construct(DBConfiguration $DBConfig);
    public function connect();
    public function close();
    public function results($fields,$conditions,$table);
    public function insertRecord($recordData,$table);
    public function updateRecord($recordID,$recordData,$table);
    public function deleteRecord($recordID,$table);
    public function deleteManyRecords($conditions,$table);

}