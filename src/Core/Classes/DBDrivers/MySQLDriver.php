<?php

namespace Core\Classes\DBDrivers;

use Exception;
use mysqli;
use mysqli_result;

class MySQLDriver extends SQLBaseDriver
{
    public function connect(): mysqli
    {
        if (!$this->isLinked()) {
            $this->link = new mysqli(
                $this->DBConfig->getHost(),
                $this->DBConfig->getUsername(),
                $this->DBConfig->getPassword(),
                $this->DBConfig->getDB(),
                $this->DBConfig->getPort(),
                $this->DBConfig->getSocket()
            );
        } else {
            $this->link()->connect(
                $this->DBConfig->getHost(),
                $this->DBConfig->getUsername(),
                $this->DBConfig->getPassword(),
                $this->DBConfig->getDB(),
                $this->DBConfig->getPort(),
                $this->DBConfig->getSocket()
            );
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
        if ($result instanceof mysqli_result) {
            $result->free_result();
        }
    }

    public function getInsertedID(mixed $result = null): int|string|null
    {
        if (!$this->isLinked()) {
            return null;
        }
        return $this->link()->insert_id;
    }

    public function fetch_assoc(mixed $result): array|bool|null
    {
        if ($result instanceof mysqli_result) {
            return $result->fetch_assoc();
        }
        return null;
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
        return $this->link && $this->link instanceof mysqli;
    }

    public function link(): mysqli
    {
        if ($this->link instanceof mysqli) {
            return $this->link;
        }
        throw new Exception("Error Processing Request", 1);
    }
}
