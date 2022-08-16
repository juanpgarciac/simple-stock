<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IDBDriver;
use Core\Traits\SQLUtils;
use PgSql;
use PHPUnit\phpDocumentor\Reflection\Types\Resource_;

class PostgreSQLDriver implements IDBDriver
{
    use SQLUtils;

    private DBConfiguration $DBConfig;
    private $link = null;


    public function __construct(DBConfiguration $DBConfig)
    {
        $this->DBConfig = $DBConfig;
    }

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


    public function results($fields, $conditions, $table): mixed
    {
        $records = [];
        $this->connect();
        $query = SQLUtils::selectQuery($fields, $conditions, $table);
        $result = $this->query($query);
        while ($row =  pg_fetch_assoc($result)
        ) {
            $records[] = $row;
        }
        pg_free_result($result);
        $this->close();
        return $records;
    }

    public function resultByID($recordID, $table, $id_field = 'id'): mixed
    {
        $results = $this->results(['*'], ["$id_field = $recordID"], $table);
        return count($results)>0 ? $results[0] : null;
    }

    public function insertRecord($recordData, $table, $id_field = 'id'): string
    {
        $id = null;
        $this->connect();
        $query = SQLUtils::insertQuery($recordData, $table, " RETURNING $id_field");
        if ($result = $this->query($query)) {
            $id = pg_fetch_row($result)[0];
        }
        $this->close();
        return $id;
    }

    public function updateRecord($recordID, $recordData, $table, $id_field = 'id'): string
    {
        $this->connect();
        $query = SQLUtils::updateQuery($recordData, ["id = $recordID"], $table);
        $this->query($query);
        $this->close();

        return $recordID;
    }

    public function deleteRecord($recordID, $table, $id_field = 'id'): void
    {
        $this->deleteManyRecordsByID([$recordID], $table, $id_field = 'id');
    }

    public function deleteManyRecordsByID(array $recordIDs, string $table, string $id_field = 'id'): void
    {
        $this->connect();
        $recordIDs = implode(", ", $recordIDs);
        $query = SQLUtils::deleteQuery(["$id_field in ( $recordIDs )"], $table);
        $this->query($query);
        $this->close();
    }

    public function deleteManyRecords($conditions, $table): void
    {
        $this->connect();
        $query = SQLUtils::deleteQuery($conditions, $table);
        $this->query($query);
        $this->close();
    }

    public function query(string $query): mixed
    {
        return pg_query($this->link, ($query));
    }
}
