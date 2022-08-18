<?php

namespace Core\Classes\StorageDrivers;

use Core\Classes\DBDrivers\PDODBDriverClass;
use PDO;
use PDOException;
use Core\Traits\SQLUtils;
use PDOStatement;

class PDODriver extends SQLBaseDriver
{
    protected function connect(): PDO
    {
        $driver = PDODBDriverClass::checkPDODriverAvailability($this->DBConfig->getDriver());
        if ($driver == 'sqlite') {
            $dsn = "$driver:".$this->DBConfig->getDB()."";
        } else {
            $dsn = "$driver:dbname=".$this->DBConfig->getDB().";host=".$this->DBConfig->getHost().";port=".$this->DBConfig->getPort().";";
        }
        return new PDO($dsn, $this->DBConfig->getUsername(), $this->DBConfig->getPassword());
    }

    protected function close(): void
    {
        //connection close by its own
    }

    protected function free_result(mixed $result): void
    {
        $result = null;
    }

    protected function getInsertedID(mixed $result = null): int | string | null
    {
        return $this->link()->lastInsertId();
    }

    protected function fetch_assoc(mixed $result): array|false|null
    {
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    protected function query(string $query): mixed
    {
        return $this->link()->query($query, PDO::FETCH_ASSOC);
    }

    protected function processQuery(string $query): bool
    {
        return self::is_result($this->link()->query($query), PDOStatement::class);
    }
}
