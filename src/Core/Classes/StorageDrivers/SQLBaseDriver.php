<?php

namespace Core\Classes\StorageDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IStorageDriver;
use Core\Traits\SQLUtils;

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
    abstract protected function connect(): mixed;
    /**
     * @return void
     */
    abstract protected function close(): void;

    private function commonConnect(): mixed
    {
        if (!$this->isLinked()) {
            $this->link = $this->connect();
        }
        $this->nativeClass = self::class_or_resource($this->link);
        return $this->link;
    }

    protected function commonClose(): void
    {
        if ($this->isLinked()) {
            $this->close();
        }
        $this->link = null;
    }

    /**
     * @param mixed $obj
     *
     * @return string
     */
    private static function class_or_resource(mixed $obj)
    {
        return !is_resource($obj) ? $obj::class : get_resource_type($obj);
    }

    public function insertRecord($recordData, $table, $id_field = 'id'): string|int|null
    {
        $id = null;
        $query = SQLUtils::insertQuery($recordData, $table);
        if ($this->processQuery($query)) {
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
        if($result){
            $nativeResult = self::class_or_resource($result);
            while ($row =  $this->commonFetch($result, $nativeResult)) {
                $records[] = $row;
            }
            $this->free_result($result);
        }
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
        $query = SQLUtils::updateQuery($recordData, ["$id_field = $recordID"], $table);
        $this->commonProcessQuery($query);
        return $recordID;
    }

    public function deleteRecord($recordID, $table, $id_field = 'id'): void
    {
        $this->deleteManyRecordsByID([$recordID], $table, $id_field);
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

    private function commonProcessQuery(string $query): void
    {
        $this->processQuery($query);
        $this->commonClose();
    }

    protected function isLinked(): bool
    {
        return !is_null($this->link) && (is_resource($this->link) || (!is_null($this->nativeClass) && $this->link instanceof $this->nativeClass));
    }

    protected function isconnected(): bool
    {
        return $this->isLinked();
    }

    protected function link(): mixed
    {
        if (!$this->isconnected()) {
            $this->link = $this->commonConnect();
        }
        return $this->link;
    }

    /**
     * @param mixed $result
     * @param string $nativeResult
     *
     * @return bool
     */
    protected static function is_result(mixed $result, string $nativeResult): bool
    {
        return !is_null($result) && (is_resource($result) || ($result instanceof $nativeResult));
    }

    /**
     * @param mixed $result
     * @param string $nativeResult
     *
     * @return array<mixed>|false|null
     */
    private function commonFetch(mixed $result, $nativeResult): array|false|null
    {
        if (self::is_result($result, $nativeResult)) {
            return $this->fetch_assoc($result);
        }
        return false;
    }

    abstract protected function free_result(mixed $result): void;

    abstract protected function getInsertedID(): int | string | null;

    /**
     * @param mixed $result
     *
     * @return array<mixed>|false|null
     */
    abstract protected function fetch_assoc(mixed $result): array|false|null;

    abstract protected function query(string $query): mixed;

    abstract protected function processQuery(string $query): bool;
}
