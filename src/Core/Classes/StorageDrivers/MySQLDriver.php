<?php

namespace Core\Classes\StorageDrivers;

use Exception;
use mysqli;
use mysqli_result;

class MySQLDriver extends SQLBaseDriver
{
    protected function connect(): mysqli
    {
        return new mysqli(
            $this->DBConfig->getHost(),
            $this->DBConfig->getUsername(),
            $this->DBConfig->getPassword(),
            $this->DBConfig->getDB(),
            $this->DBConfig->getPort(),
            $this->DBConfig->getSocket()
        );
    }

    protected function close(): void
    {
        $this->link()->close();
    }

    protected function free_result(mixed $result): void
    {
        if (self::is_result($result, mysqli_result::class)) {
            $result->free_result();
        }
    }

    protected function getInsertedID(): int|string|null
    {
        return $this->link()->insert_id;
    }

    protected function fetch_assoc(mixed $result): array|false|null
    {
        if (self::is_result($result, mysqli_result::class)) {
            return $result->fetch_assoc();
        }
        return null;
    }

    protected function query(string $query): mixed
    {
        $this->link()->real_query($query);
        return $this->link()->store_result();
    }

    protected function processQuery(string $query): bool
    {
        return $this->link()->query($query);
    }
}
