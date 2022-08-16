<?php

namespace Core\Classes\DBDrivers;

use Core\Interfaces\StorageMapper;
use Core\Traits\Utils;
use Exception;

class FakeDBDriver implements StorageMapper
{
    /**
     * @var array<mixed>
     */
    private array $tables = [];
    /**
     * @var array<int>
     */
    private array $tables_ids = [];

    public function results($fields, $conditions, $table): mixed
    {
        return array_filter($this->tables[$table], function ($row) use ($conditions) {
            foreach ($conditions as $condition) {
                $arr = explode(' ', $condition);
                $field = $arr[0];
                $operator = $arr[1];
                $compare = trim(implode(" ", array_slice($arr, 2)), "\'");
                if (!Utils::operate($row[$field], $operator, trim($compare, '\''))) {
                    return false;
                }
            }
            return true;
        });
    }

    public function insertRecord(array $recordData, string $table, string $id_field = 'id'): string
    {
        if (!isset($this->tables[$table])) {
            $this->tables[$table] = [];
        }
        $id = self::getNewID($table);

        $recordData[$id_field] = $id;

        $this->tables[$table][ $id ] = $recordData;

        return $id;
    }


    public function updateRecord($recordID, $recordData, $table, string $id_field = 'id'): string
    {
        if (!isset($this->tables[$table]) || !isset($this->tables[$table][$recordID])) {
            throw new Exception("Record not found", 1);
        }

        $recordToBeUpdated = &$this->tables[$table][$recordID];
        foreach ($recordData as $field => $value) {
            if ($field == $id_field) {
                continue;
            }

            if (isset($recordToBeUpdated[$field])) {
                $recordToBeUpdated[$field] = $value;
            }
        }

        return $recordToBeUpdated[$id_field];
    }

    public function deleteRecord($recordID, $table, $id_field = 'id'): void
    {
        $this->deleteManyRecordsByID([$recordID], $table);
    }

    public function deleteManyRecordsByID($recordIDs, $table, $id_field = 'id'): void
    {
        foreach ($recordIDs as $id) {
            if (isset($this->tables[$table][$id])) {
                unset($this->tables[$table][$id]);
            }
        }
    }

    public function deleteManyRecords($conditions, $table): void
    {
        if (empty($conditions)) {
            $this->tables[$table]  = [];
            return;
        }

        $this->tables[$table]  = array_filter($this->tables[$table], function ($row) use ($conditions) {
            foreach ($conditions as $condition) {
                list($field, $operator, $compare) = explode(' ', $condition);
                if (!Utils::operate($row[$field], $operator, $compare)) {
                    return false;
                }
            }
            return true;
        });
    }

    public function resultByID($recordID, $table, $id_field = 'id'): mixed
    {
        return isset($this->tables[$table]) && isset($this->tables[$table][$recordID]) ? $this->tables[$table][$recordID] : null;
    }

    private function getNewID(string $table): string
    {
        if (!isset($this->tables_ids[$table])) {
            $this->tables_ids[$table] = 0;
        }

        $this->tables_ids[$table]++;

        return $this->tables_ids[$table]."";
    }
}
