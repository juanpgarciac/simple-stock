<?php

namespace Core\Classes\DBDrivers;

use Exception;
use SQLite3;
use SQLite3Result;

class SQLite3Driver extends SQLBaseDriver
{
    public function connect(): SQLite3
    {
        if (!$this->isLinked()) {
            $this->link = new SQLite3($this->DBConfig->getDB(), SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $this->DBConfig->getPassword());
        }
        return $this->link();
    }

    public function close(): void
    {
        if ($this->isLinked()) {
            $this->link()->close();
        }
        $this->link = null;
    }

    public function free_result(mixed $result): void
    {
    }

    public function getInsertedID(mixed $result = null): int | string | null
    {
        if (!$this->isLinked()) {
            return null;
        }
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
        if (!$this->isLinked()) {
            return null;
        }
        return $this->link()->query($query);
    }

    public function isLinked(): bool
    {
        return $this->link && ($this->link instanceof SQLite3);
    }

    public function link(): SQLite3
    {
        if ($this->link instanceof SQLite3) {
            return $this->link;
        }
        throw new Exception("Error Processing Request", 1);
    }
}
