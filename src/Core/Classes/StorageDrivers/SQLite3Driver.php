<?php

namespace Core\Classes\StorageDrivers;

use Exception;
use SQLite3;
use SQLite3Result;

class SQLite3Driver extends SQLBaseDriver
{
    public function connect(): SQLite3
    {
        return new SQLite3($this->DBConfig->getDB(), SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $this->DBConfig->getPassword());
    }

    public function close(): void
    {
        $this->link()->close();
    }

    public function free_result(mixed $result): void
    {
    }

    public function getInsertedID(mixed $result = null): int | string | null
    {
        return $this->link()->lastInsertRowID();
    }

    public function fetch_assoc($result): array|bool|null
    {
        if ($result instanceof SQLite3Result) {
            return $result->fetchArray();
        }
        return false;
    }

    public function query(string $query): mixed
    {
        return $this->link()->query($query);
    }

    public function processQuery(string $query):bool
    {
        return $this->link()->exec($query);
    }

}
