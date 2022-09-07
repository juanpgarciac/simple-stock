<?php

namespace Core\Classes;

use Core\Interfaces\IStorageDriver;
use Core\Traits\QueryBuilder\QueryBuilder;

abstract class ModelRepository
{
    use QueryBuilder;

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
     * @var array<mixed>
     */
    protected array $results = [];

    /**
     * @var string
     */
    protected string $modelClass = Model::class;

    /**
     * @var array
     */
    protected array $nullable = [];

    /**
     * @param IStorageDriver $DBDriver
     */
    final public function __construct(IStorageDriver $DBDriver)
    {
        $this->DB = $DBDriver;
        $this->fields = array_merge($this->fields, $this->nullable);
    }

    /**
     * @return string
     */
    public function getDBClass(): string
    {
        return $this->DB::class;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
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
        return !empty($this->getSelectQueryArray()) ? $this->getSelectQueryArray() : ['*'];
    }

    /**
     * @return array<string>
     */
    public function getConditions(): array
    {
        return $this->getWhereQueryArray();
    }

    /**
     * @param bool $cached
     *
     * @return array<mixed>
     */
    public function results(bool $cached = false): array
    {
        if (!$cached || empty($this->results)) {
            $this->results = $this->DB->results($this->getFieldSelection(), $this->getConditions(), $this->getFrom(), $this->id_field, $this->getOrderQuery());
        }
        $this->clear_query();
        return $this->results;
    }

    /**
     * @return void
     */
    private function clear_query(): void
    {
        $this->clearQuery();
    }


    /**
     * @param Model $modelRecord
     *
     * @return Model
     */
    public function insert(Model|array $modelRecord): Model|array|false
    {
        $insertArray = $this->getInsertArray($modelRecord, true);
        if (!empty($insertArray)) {
            $id = $this->DB->insertRecord($insertArray, $this->getTable(), $this->id_field);
            if (!is_null($id)) {//ID null, why ?
                return $this->find($id);
            }//get whole record updated
        }
        return false;
    }

    /**
     * @param Model $modelRecord
     *
     * @return Model
     */
    public function update(Model|array $modelRecord): Model|array|false
    {
        $insertArray = $this->getInsertArray($modelRecord);

        if (!empty($insertArray)) {
            $modelID = null;
            if (isset($insertArray[$this->id_field])) {
                $modelID = $insertArray[$this->id_field];
            } elseif (is_subclass_of($modelRecord, Model::class)) {
                $modelID = $modelRecord->id($this->id_field);
            }
            if (!is_null($modelID)) {
                $id = $this->DB->updateRecord($modelID, $insertArray, $this->getTable(), $this->id_field);
                $modelRecord = $this->find($id);
                return $modelRecord;
            }
        }
        return false;
    }

    /**
     * @param Model|array $modelRecord
     *
     * @return array
     */
    private function getInsertArray(Model|array $modelRecord, $ignoreIDField = false): array
    {
        $insertArray = [];
        $record = is_array($modelRecord) ? $modelRecord : $modelRecord->toArray();

        foreach ($this->fields as $field) {
            if ($field == $this->id_field && $ignoreIDField) {
                continue;
            }
            if (array_key_exists($field, $record) && (!is_null($record[$field]) || in_array($field, $this->nullable))) {
                $insertArray[$field] = $record[$field];
            }
        }
        return $insertArray;
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
    public function find(string|int $recordID): Model|array|null
    {
        $result = $this->where($this->getTable().'.'.$this->id_field, $recordID)->results();
        $this->clear_query();
        //$result = $this->DB->resultByID($recordID, $this->getTable(), $this->id_field);
        if (empty($result)) {
            return null;
        }

        $result = $result[array_key_first($result)];

        if (!empty($this->getModelClass())) {
            if (is_subclass_of($this->getModelClass(), Model::class)) {
                return $this->getModelClass()::fromState($result);
            }//return model instance
            else {
                trigger_error("{$this->getModelClass()} should extend ".Model::class, E_USER_WARNING);
            }
        }

        return $result;//return the result assoc array
    }

    private function getFrom(): string
    {
        return trim($this->getTable().' '.$this->getJoinQuery());
    }

    private function from($from)
    {
        return $this->getTable();
    }
}
