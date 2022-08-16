<?php

namespace Core\Classes\DBDrivers;

use mysqli;

class MySQLDriver extends SQLBaseDriver
{
    public function connect(): mysqli
    {
        if (!$this->link || !is_a($this->link, 'mysqli')) {
            $this->link = new mysqli(
                $this->DBConfig->getHost(),
                $this->DBConfig->getUsername(),
                $this->DBConfig->getPassword(),
                $this->DBConfig->getDB(),
                $this->DBConfig->getPort(),
                $this->DBConfig->getSocket()
            );
        } else {
            $this->link->connect(
                $this->DBConfig->getHost(),
                $this->DBConfig->getUsername(),
                $this->DBConfig->getPassword(),
                $this->DBConfig->getDB(),
                $this->DBConfig->getPort(),
                $this->DBConfig->getSocket()
            );
        }
        return $this->link;
    }

    public function close(): void
    {
        if ($this->link && is_a($this->link, 'mysqli')) {
            $this->link->close();
        }
        $this->link = null;
    }

    public function free_result(mixed $result): void
    {
        $result->free_result();
    }

    public function getInsertedID(mixed $result = null): int|string|null
    {
        return $this->link->insert_id;
    }

    public function fetch_assoc(mixed $result): mixed
    {
        return $result->fetch_assoc();
    }

    public function query(string $query): mixed
    {
        return $this->link->query($query);
    }
}
