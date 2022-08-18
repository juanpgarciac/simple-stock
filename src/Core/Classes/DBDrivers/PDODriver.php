<?php

namespace Core\Classes\DBDrivers;

use PDO;
use PDOException;
use PDOStatement;
use Core\Traits\SQLUtils;

class PDODriver extends SQLBaseDriver
{

    public function connect(): PDO
    {
        if (!$this->isLinked()) {
            $driver = PDODBDriver::checkPDODriverAvailability($this->DBConfig->getDriver());
            if($driver == 'sqlite'){
                $dsn = "$driver:".$this->DBConfig->getDB()."";
            }else{
                $dsn = "$driver:dbname=".$this->DBConfig->getDB().";host=".$this->DBConfig->getHost().";port=".$this->DBConfig->getPort().";";
            }
            
            $this->link = new PDO($dsn,$this->DBConfig->getUsername(),$this->DBConfig->getPassword(), [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        }
        return $this->link();
    }

    public function close(): void
    {
        $this->link = null;
    }

    public function free_result(mixed $result): void
    {
        $result = null;
    }

    public function getInsertedID(mixed $result = null): int | string | null
    {
        if (!$this->isLinked()) {
            return null;
        }
        return $this->link()->lastInsertId();
    }

    public function results($fields, $conditions, $table): array
    {
        $records = [];
        $this->connect();
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
        return $result->getIterator()->next();
    }

    public function query(string $query): mixed
    {
        if (!$this->isLinked()) {
            return null;
        }
        return $this->link()->query($query);
    }

    public function isLinked(): bool
    {
        return $this->link && ($this->link instanceof PDO);
    }

    public function link(): PDO
    {
        if ($this->link instanceof PDO) {
            return $this->link;
        }
        throw new PDOException("Error Processing Request", 1);
    }
}
