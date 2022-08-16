<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IDBDriver;
use Core\Traits\SQLUtils;
use SQLite3;

class SQLite3Driver implements IDBDriver
{
    use SQLUtils;

    private DBConfiguration $DBConfig;
    private $link = null;


    public function __construct(DBConfiguration $DBConfig)
    {
        $this->DBConfig = $DBConfig;
    }

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


    public function results($fields, $conditions, $table): mixed
    {
        $records = [];
        $this->connect();
        $query = SQLUtils::selectQuery($fields, $conditions, $table);
        $result = $this->link->query($query);
        while ($row =  $result->fetchArray()) {
            $records[] = $row;
        }
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
        $query = SQLUtils::insertQuery($recordData, $table);
        if ($this->link->query($query)) {
            $id = $this->link->lastInsertRowID();
        }
        $this->close();
        return $id;
    }

    public function updateRecord($recordID, $recordData, $table, $id_field = 'id'): string
    {
        $this->connect();
        $query = SQLUtils::updateQuery($recordData, ["id = $recordID"], $table);
        $this->link->query($query);
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
        $this->link->query($query);
        $this->close();
    }

    public function deleteManyRecords($conditions, $table): void
    {
        $this->connect();
        $query = SQLUtils::deleteQuery($conditions, $table);
        $this->link->query($query);
        $this->close();
    }
}
