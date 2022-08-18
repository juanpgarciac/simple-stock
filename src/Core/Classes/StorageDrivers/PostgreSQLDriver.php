<?php

namespace Core\Classes\StorageDrivers;

use Exception;
use PgSql\Connection as Postgres;
use PgSql\Result as Result;

class PostgreSQLDriver extends SQLBaseDriver
{
    public function connect(): mixed
    {
        return pg_connect("host={$this->DBConfig->getHost()} user={$this->DBConfig->getUsername()} password={$this->DBConfig->getPassword()} dbname={$this->DBConfig->getDB()} port={$this->DBConfig->getPort()}");
    }

    public function close(): void
    {
        pg_close($this->link());
    }

    public function insertRecord($recordData, $table, $id_field = 'id'): int | string | null
    {
        $id = null;
        $query = self::insertQuery($recordData, $table, " RETURNING $id_field");
        $result = $this->query($query);
        $id = $this->getInsertedID($result);
        $this->close();
        return $id;
    }

    public function free_result(mixed $result): void
    {
        if (!is_null($result) && ($result instanceof Result || is_resource($result))) {
            pg_free_result($result);
        }
    }

    public function getInsertedID(mixed $result = null): int | string | null
    {
        if (!is_null($result) && ($result instanceof Result || is_resource($result))) {
            $row = pg_fetch_row($result);
            return is_array($row) ? $row[0] : null;
        }
        return null;
    }

    public function fetch_assoc(mixed $result): array|bool|null
    {
        if (!is_null($result) && ($result instanceof Result || is_resource($result))) {
            return pg_fetch_assoc($result);
        }
        return null;
    }

    public function query(string $query): mixed
    {
        return pg_query($this->link(), $query);
    }

    public function processQuery(string $query):bool
    {
        return is_resource(pg_query($this->link(), $query));
    }
}
