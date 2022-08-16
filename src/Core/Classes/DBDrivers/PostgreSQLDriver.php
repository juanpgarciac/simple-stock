<?php

namespace Core\Classes\DBDrivers;

use Core\Traits\SQLUtils;

class PostgreSQLDriver extends SQLBaseDriver
{
    public function connect(): mixed
    {
        if (!$this->link || (!is_a($this->link, 'PgSql\Connection') && !is_a($this->link, 'resource'))) {
            $this->link = pg_connect("host={$this->DBConfig->getHost()} user={$this->DBConfig->getUsername()} password={$this->DBConfig->getPassword()} dbname={$this->DBConfig->getDB()} port={$this->DBConfig->getPort()}");
        }
        return $this->link;
    }

    public function close(): void
    {
        if ($this->link && (is_a($this->link, 'PgSql\Connection') || is_a($this->link, 'resource'))) {
            pg_close($this->link);
        }
        $this->link = null;
    }

    public function insertRecord($recordData, $table, $id_field = 'id'): string
    {
        $id = null;
        $this->connect();
        $query = SQLUtils::insertQuery($recordData, $table, " RETURNING $id_field");
        $result = $this->query($query);
        $id = $this->getInsertedID($result);
        $this->close();
        return $id;
    }

    public function free_result(mixed $result): void
    {
        pg_free_result($result);
    }

    public function getInsertedID(mixed $result = null): int | string | null
    {
        if ($result) {
            return pg_fetch_row($result)[0];
        }
        return null;
    }

    public function fetch_assoc(mixed $result): mixed
    {
        return pg_fetch_assoc($result);
    }

    public function query(string $query): mixed
    {
        return pg_query($this->link, ($query));
    }
    
}
