<?php

namespace Core\Classes;

use Core\Interfaces\IStorageDriver;
use InvalidArgumentException;

abstract class ModelRepository
{
    /**
     * @var string
     */
    protected string $table = '';
    /**
     * @var array<string>
     */
    protected array $fields = [];

    /**
     * @var string
     */
    protected string $id_field = 'id';
    /**
     * @var IStorageDriver
     */
    protected IStorageDriver $DB;
    /**
     * @var array<string>
     */
    protected array $select = ['*'];
    /**
     * @var array<string>
     */
    protected array $where = [];
    /**
     * @var array<mixed>
     */
    protected array $results = [];

    /**
     * @param IStorageDriver $DBDriver
     */
    public function __construct(IStorageDriver $DBDriver)
    {
        $this->DB = $DBDriver;
    }

    /**
     * @return string
     */
    public function getDBClass(): string
    {
        return $this->DB::class;
    }

    /**
     * @param array<string>|string $fields
     *
     * @return ModelRepository
     */
    public function select(array|string $fields = '*'): ModelRepository
    {
        if (!is_array($fields)) {
            $this->select = explode(',', $fields);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return array<string>
     */
    public function getFieldSelection(): array
    {
        return $this->select;
    }

    /**
     * @return array<string>
     */
    public function getConditions(): array
    {
        return $this->where;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param string $compare
     *
     * @return ModelRepository
     */
    public function where(string $field, string $operator, string $compare): ModelRepository
    {
        if (!in_array($operator, ['=','>','<','>=','<=','like','<>','!='])) {
            throw new InvalidArgumentException("Invalid comparisor operator", 1);
        }

        $this->where[] = "$field $operator '$compare'";

        return $this;
    }

    /**
     * @param bool $cached
     *
     * @return array<mixed>
     */
    public function results(bool $cached = false): array
    {
        if (!$cached || empty($this->results)) {
            $this->results = $this->DB->results($this->getFieldSelection(), $this->getConditions(), $this->getTable());
        }
        $this->clear_query();
        return $this->results;
    }

    /**
     * @return void
     */
    private function clear_query(): void
    {
        $this->select = ['*'];
        $this->where = [];
    }


    /**
     * @param Model $modelRecord
     *
     * @return Model
     */
    public function insert(Model $modelRecord): Model
    {
        $insertArray = [];


        foreach ($this->fields as $field) {
            $value = $modelRecord->getValue($field);
            $insertArray[$field] = $value;
        }

        if (!empty($insertArray)) {
            $id = $this->DB->insertRecord($insertArray, $this->getTable(), $this->id_field);

            $modelRecord->setValue($this->id_field, $id);
        }


        return $modelRecord;
    }

    /**
     * @param Model $modelRecord
     *
     * @return Model
     */
    public function update(Model $modelRecord): Model
    {
        $insertArray = [];

        foreach ($this->fields as $field) {
            $value = $modelRecord->getValue($field);
            $insertArray[$field] = $value;
        }

        if (!empty($insertArray)) {
            $this->DB->updateRecord($modelRecord->id($this->id_field), $insertArray, $this->getTable(), $this->id_field);
        }

        return $modelRecord;
    }

    /**
     * @param array<int|string>|int|string $recordIDs
     *
     * @return void
     */
    public function delete(array|int|string $recordIDs): void
    {
        if (is_array($recordIDs)) {
            $this->DB->deleteManyRecordsByID($recordIDs, $this->getTable());
        } else {
            $this->DB->deleteRecord($recordIDs, $this->getTable());
        }
    }

    /**
     * @param bool $allowDeleteWithEmptyConditions
     *
     * @return bool
     */
    public function deleteBatch(bool $allowDeleteWithEmptyConditions = false): bool
    {
        if (empty($this->getConditions()) && !$allowDeleteWithEmptyConditions) {
            return false;
        }

        $this->DB->deleteManyRecords($this->getConditions(), $this->getTable());
        $this->clear_query();
        return true;
    }

    /**
     * @param string|int $recordID
     *
     * @return Model|null
     */
    public function find(string|int $recordID): Model|null
    {
        $this->clear_query();
        $result = $this->DB->resultByID($recordID, $this->getTable(), $this->id_field);
        if (!empty($result)) {
            return $this::class::fromState($result);
        }
        return null;
    }

    /**
     * @param array<mixed> $recordArray
     *
     * @return Model
     */
    abstract public static function fromState(array $recordArray): Model;
}
