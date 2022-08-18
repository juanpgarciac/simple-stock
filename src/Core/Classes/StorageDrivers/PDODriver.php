<?php

namespace Core\Classes\StorageDrivers;

use Core\Classes\DBDrivers\PDODBDriverClass;
use PDO;
use PDOException;
use Core\Traits\SQLUtils;
use PDOStatement;

class PDODriver extends SQLBaseDriver
{

    public function connect(): PDO
    {
        $driver = PDODBDriverClass::checkPDODriverAvailability($this->DBConfig->getDriver());
        if($driver == 'sqlite'){
            $dsn = "$driver:".$this->DBConfig->getDB()."";
        }else{
            $dsn = "$driver:dbname=".$this->DBConfig->getDB().";host=".$this->DBConfig->getHost().";port=".$this->DBConfig->getPort().";";
        }            
        return new PDO($dsn,$this->DBConfig->getUsername(),$this->DBConfig->getPassword());
    }

    public function close(): void
    {
        
    }

    public function free_result(mixed $result): void
    {
        $result = null;
    }

    public function getInsertedID(mixed $result = null): int | string | null
    {
        return $this->link()->lastInsertId();
    }

    public function results($fields, $conditions, $table): array
    {
        $records = [];
        $query = SQLUtils::selectQuery($fields, $conditions, $table);
        $result = $this->query($query);
        foreach ($result as $row) {
            $records[] = $row;
        }
        $this->free_result($result);
        $this->close();
        return $records;
    }

    public function fetch_assoc(mixed $result): mixed
    {
        return $result;
    }

    public function query(string $query): mixed
    {
        return $this->link()->query($query, PDO::FETCH_ASSOC);
    }

    public function processQuery(string $query): bool
    {
        return $this->link()->query($query) instanceof PDOStatement;
    }

}
