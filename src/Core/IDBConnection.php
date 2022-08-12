<?php

namespace Core;

interface IDBConnection
{
    public function __construct(DBConfig $DBConfig);
    public function connect();
    public function close();
    public function results($fields,$conditions,$table);
    public function insert($fields, $values, $table);
    public static function query($fields,$conditions,$table);
}