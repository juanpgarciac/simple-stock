<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IDBDriver;
use Core\Traits\SQLUtils;
use mysqli;

class MySQLDriver implements IDBDriver
{
    use SQLUtils;

    private DBConfiguration $DBConfig;
    private $link = null;


    public function __construct(DBConfiguration $DBConfig)
    {
        $this->DBConfig = $DBConfig;
    }

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


    public function results($fields, $conditions, $table): mixed
    {
        $records = [];
        $this->connect();
        $query = SQLUtils::selectQuery($fields, $conditions, $table);
        $result = $this->query($query);
        while ($row =  $result->fetch_assoc()) {
            $records[] = $row;
        }
        $result->free_result();
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
            $id = $this->link->insert_id;
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

    public function query($query): mixed
    {
        return $this->link->query($query);
    }
}
