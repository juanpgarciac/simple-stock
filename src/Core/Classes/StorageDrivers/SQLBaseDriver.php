<?php

namespace Core\Classes\StorageDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IStorageDriver;
use Core\Traits\SQLUtils;
use Exception;

abstract class SQLBaseDriver implements IStorageDriver
{
    use SQLUtils;

    /**
     * @var DBConfiguration
     */
    protected DBConfiguration $DBConfig;

    /**
     * @var mixed|null
     */
    private mixed $link = null;

    private ?string $nativeClass = null;

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

    private function commonConnect(): mixed
    {
        if (!$this->isLinked()) {
            $this->link = $this->connect();
        }
        $this->nativeClass = self::class_or_resource($this->link);
        return $this->link;
    }

    private function commonClose():void
    {
        if ($this->isLinked()) {
            $this->close();
        }
        $this->link = null;
    }

    private static function class_or_resource($obj)
    {
        return !is_resource($obj) ? $obj::class : get_resource_type($obj);
    }

    public function insertRecord($recordData, $table, $id_field = 'id'): string|int|null
    {
        $id = null;
        $query = SQLUtils::insertQuery($recordData, $table);
        if ($this->processQuery($query,false)) {
            $id = $this->getInsertedID();
        }
        $this->commonClose();
        return $id;
    }

    public function results($fields, $conditions, $table): array
    {
        $records = [];
        $query = SQLUtils::selectQuery($fields, $conditions, $table);
        $result = $this->query($query);
        while ($row =  $this->fetch_assoc($result)) {
            if (is_array($row)) {
                $records[] = $row;
            }
        }
        $this->free_result($result);
        $this->commonClose();
        return $records;
    }

    public function resultByID($recordID, $table, $id_field = 'id'): array|null
    {
        $results = $this->results(['*'], ["$id_field = $recordID"], $table);
        return count($results)>0 ? $results[0] : null;
    }

    public function updateRecord($recordID, $recordData, $table, $id_field = 'id'): string|int
    {
        $query = SQLUtils::updateQuery($recordData, ["id = $recordID"], $table);
        $this->commonProcessQuery($query);
        return $recordID;
    }

    public function deleteRecord($recordID, $table, $id_field = 'id'): void
    {
        $this->deleteManyRecordsByID([$recordID], $table, $id_field = 'id');
    }

    public function deleteManyRecordsByID(array $recordIDs, string $table, string $id_field = 'id'): void
    {
        $recordIDs = implode(", ", $recordIDs);
        $query = SQLUtils::deleteQuery(["$id_field in ( $recordIDs )"], $table);
        $this->commonProcessQuery($query);
    }

    public function deleteManyRecords($conditions, $table): void
    {
        $query = SQLUtils::deleteQuery($conditions, $table);
        $this->commonProcessQuery($query);
    }

    private function commonProcessQuery(string $query):void
    {
        $this->processQuery($query);
        $this->commonClose();
    }

    public function isLinked(): bool
    {
        return !is_null($this->link) && ( is_resource($this->link) || is_a($this->link,$this->nativeClass));
    }
    
    public function isconnected(): bool{
        return $this->isLinked();
    }

    public function link(): mixed
    {
        if (!$this->isconnected()) {
            $this->link = $this->commonConnect();
        }        
        return $this->link;
    }

    abstract public function free_result(mixed $result): void;

    abstract public function getInsertedID(mixed $result = null): int | string | null;

    abstract public function fetch_assoc(mixed $result): mixed;

    abstract public function query(string $query): mixed;

    abstract public function processQuery(string $query):bool;
}
