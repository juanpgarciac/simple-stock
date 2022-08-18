<?php

namespace Core\Classes\StorageDrivers;

use Exception;
use SQLite3;
use SQLite3Result;

class SQLite3Driver extends SQLBaseDriver
{
    protected function connect(): SQLite3
    {
        return new SQLite3($this->DBConfig->getDB(), SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $this->DBConfig->getPassword());
    }

    protected function close(): void
    {
        $this->link()->close();
    }

    protected function free_result(mixed &$result): void
    {
        $result = null;
    }

    protected function getInsertedID(mixed $result = null): int | string | null
    {
        return $this->link()->lastInsertRowID();
    }

    protected function fetch_assoc($result): array|false|null
    {
        return $result->fetchArray();
    }

    protected function query(string $query): mixed
    {
        return $this->link()->query($query);
    }

    protected function processQuery(string $query):bool
    {
        return $this->link()->exec($query);
    }

}
