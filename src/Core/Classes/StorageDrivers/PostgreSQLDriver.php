<?php

namespace Core\Classes\StorageDrivers;

use PgSql\Result as Result;

class PostgreSQLDriver extends SQLBaseDriver
{
    protected function connect(): mixed
    {
        return pg_connect("host={$this->DBConfig->getHost()} user={$this->DBConfig->getUsername()} password={$this->DBConfig->getPassword()} dbname={$this->DBConfig->getDB()} port={$this->DBConfig->getPort()}");
    }

    protected function close(): void
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

    protected function free_result(mixed $result): void
    {
        if (self::is_result($result, Result::class)) {
            pg_free_result($result);
        }
    }

    protected function getInsertedID(mixed $result = null): int | string | null
    {
        if ($result && self::is_result($result, Result::class)) {
            $row = pg_fetch_row($result);
            return is_array($row) ? $row[0] : null;
        }
        return null;
    }

    protected function fetch_assoc(mixed $result): array|false|null
    {
        return pg_fetch_assoc($result);
    }

    protected function query(string $query): mixed
    {
        return pg_query($this->link(), $query);
    }

    protected function processQuery(string $query): bool
    {
        return self::is_result(pg_query($this->link(), $query), Result::class);
    }
}
