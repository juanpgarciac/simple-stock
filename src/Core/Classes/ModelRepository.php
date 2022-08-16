<?php

namespace Core\Classes;

use Core\Interfaces\IDB;
use Core\Traits\Utils;
use Exception;

abstract class ModelRepository
{
    protected string $table = '';
    protected array $fields = [];

    protected string $id_field = 'id';
    protected ?IDB $DB = null;
    protected ?array $select = ['*'];
    protected array $where = [];
    protected array $results = [];

    public function __construct(?IDB $DB = null)
    {
        $this->DB = $DB;
    }

    /**
     * @return string
     */
    public function getDBClass():string
    {
        if(!is_null($this->DB))
            return Utils::baseClassname($this->DB::class);
        return '';
    }

    public function select(array|String $fields = '*')
    {
        if (!is_array($fields)) {
            $this->select = explode(',', $fields);
        }
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getFieldSelection()
    {
        return $this->select;
    }

    public function getConditions()
    {
        return $this->where;
    }

    public function where($field, $operator, $compare)
    {
        if (!in_array($operator, ['=','>','<','>=','<=','like','<>','!='])) {
            throw new Exception("Invalid comparisor operator", 1);
        }

        $compare = is_string($compare) ? "'$compare'" : $compare;

        $this->where[] = "$field $operator $compare";

        return $this;
    }

    public function results($cached = false)
    {
        if (!$this->DB) {
            throw new Exception("No DB configured", 1);
        }

        if (!$cached || empty($this->results)) {
            $this->results = $this->DB->results($this->getFieldSelection(), $this->getConditions(), $this->getTable());
        }
        $this->clear_query();
        return $this->results;
    }

    private function clear_query()
    {
        $this->select = ['*'];
        $this->where = [];
    }


    public function insert(Model $modelRecord)
    {
        if (!$this->DB) {
            throw new Exception("No DB configured", 1);
        }

        if (empty($modelRecord)) {
            throw new Exception("Empty set", 1);
        }


        $insertArray = [];


        foreach ($this->fields as $field) {
            $value = $modelRecord->getValue($field);
            $insertArray[$field] = $value;
        }

        $id = $this->DB->insertRecord($insertArray, $this->getTable());

        $modelRecord->setValue($this->id_field, $id);

        return $modelRecord;
    }

    public function update(Model $modelRecord)
    {
        if (!$this->DB) {
            throw new Exception("No DB configured", 1);
        }

        if (empty($modelRecord)) {
            throw new Exception("Empty set", 1);
        }

        foreach ($this->fields as $field) {
            $value = $modelRecord->getValue($field);
            $insertArray[$field] = $value;
        }

        $this->DB->updateRecord($modelRecord->id($this->id_field), $insertArray, $this->getTable(), $this->id_field);

        return $modelRecord;
    }

    public function delete($recordIDs)
    {
        $this->DB->deleteRecord($recordIDs, $this->getTable());
    }

    public function deleteBatch($allowDeleteWithEmptyConditions = false)
    {
        if (empty($this->getConditions()) && !$allowDeleteWithEmptyConditions) {
            return false;
        }

        $this->DB->deleteManyRecords($this->getConditions(), $this->getTable());
        $this->clear_query();
        return true;
    }

    public function find($recordID)
    {
        $this->clear_query();
        $result = $this->DB->resultByID($recordID, $this->getTable());
        if (!empty($result)) {
            return $this::class::fromState($result);
        }
        return null;
    }

    abstract public static function fromState(array $recordArray): Model;
}
