<?php

namespace Core\Classes\DBDrivers;

use SQLite3;

class SQLite3Driver extends SQLBaseDriver
{

    public function connect(): SQLite3
    {
        if (!$this->link || !is_a($this->link, 'SQLite3')) {
            $this->link = new SQLite3($this->DBConfig->getDB(), SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $this->DBConfig->getPassword());
        }
        return $this->link;
    }

    public function close(): void
    {
        if ($this->link && is_a($this->link, 'SQLite3')) {
            $this->link->close();
        }
        $this->link = null;
    }

    public function free_result(mixed $result): void
    {
        
    }

    public function getInsertedID($result = null):int | string | null
    {
        return $this->link->lastInsertRowID();
    }

    public function fetch_assoc(mixed $result): mixed
    {
        return $result->fetchArray();
    }

    public function query(string $query): mixed
    {
        return $this->link->query($query);
    }
}
