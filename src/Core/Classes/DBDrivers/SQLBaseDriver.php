<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\StorageMapper;
use Core\Traits\SQLUtils;

abstract class SQLBaseDriver implements StorageMapper
{
    use SQLUtils;

    /**
     * @var DBConfiguration
     */
    protected DBConfiguration $DBConfig;

    /**
     * @var mixed|null
     */
    protected mixed $link = null;


    public function __construct(DBConfiguration $DBConfig)
    {
        $this->DBConfig = $DBConfig;
    }

    /**
     * @return mixed
     */
    abstract public function connect(): mixed;
    /**
     * @return void
     */
    abstract public function close(): void;

    public function insertRecord($recordData, $table, $id_field = 'id'): string|int|null
    {
        $id = null;
        $this->connect();
        $query = SQLUtils::insertQuery($recordData, $table);
        if ($this->query($query)) {
            $id = $this->getInsertedID();
        }
        $this->close();
        return $id;
    }

    public function results($fields, $conditions, $table): array
    {
        $records = [];
        $this->connect();
        $query = SQLUtils::selectQuery($fields, $conditions, $table);
        $result = $this->query($query);
        while ($row =  $this->fetch_assoc($result)) {
            if (is_array($row)) {
                $records[] = $row;
            }
        }
        $this->free_result($result);
        $this->close();
        return $records;
    }

    public function resultByID($recordID, $table, $id_field = 'id'): array|null
    {
        $results = $this->results(['*'], ["$id_field = $recordID"], $table);
        return count($results)>0 ? $results[0] : null;
    }

    public function updateRecord($recordID, $recordData, $table, $id_field = 'id'): string|int
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

    abstract public function isLinked(): bool;

    abstract public function link(): mixed;

    abstract public function free_result(mixed $result): void;

    abstract public function getInsertedID(mixed $result = null): int | string | null;

    /**
     * @param mixed $result
     *
     * @return array<mixed>|bool|null
     */
    abstract public function fetch_assoc(mixed $result): array|bool|null;

    abstract public function query(string $query): mixed;
}
