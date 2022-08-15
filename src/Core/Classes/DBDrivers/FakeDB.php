<?php
namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IDB;
use Core\Traits\SQLUtils;
use Core\Traits\Utils;

class FakeDB implements IDB
{
    use SQLUtils;

    private $tables = [];
    private $tables_ids = [];

    private function getNewID($table): string
    {
        if(!isset($this->tables_ids[$table])){
            $this->tables_ids[$table] = 0;
        }
        
        $this->tables_ids[$table]++;

        return $this->tables_ids[$table]."";
    }
    public function __construct(?DBConfiguration $DBConfig = null)
    {

    }

    public function connect()
    {

    }

    public function close()
    {

    }



    public function results($fields,$conditions,$table)
    {

        return array_filter($this->tables[$table],function($row) use($conditions){
            foreach($conditions as $condition){
                $arr = explode(' ',$condition);
                $field = $arr[0];
                $operator = $arr[1];
                $compare = trim(implode(" ",array_slice($arr,2)),"\'");
                if(!Utils::operate($row[$field],$operator,trim($compare,'\''))){
                    return false;
                }
            }
            return true;
        });

    }

    public function insertRecord(array $recordData, string $table):int
    {

        if(!isset($this->tables[$table] )){
            $this->tables[$table] = [];
        }
        $id = self::getNewID($table);

        $this->tables[$table][ $id ] = $recordData;

        return $id;

    }


    public function updateRecord($recordID,$recordData,$table,  $id_field = 'id')
    {

    }

    public function deleteRecord($recordID, $table,  $id_field = 'id')
    {

        $recordIDs = !is_array($recordID) ? [$recordID] : $recordID;

        foreach ($recordIDs as $id) {
            if(isset($this->tables[$table][$id]))
                unset($this->tables[$table][$id]);
        }

    }

    public function deleteManyRecords($conditions,$table)
    {        
        if(empty($conditions)){
            $this->tables[$table]  = [];
            return;
        }

        $this->tables[$table]  = array_filter($this->tables[$table],function($row) use($conditions){
            foreach($conditions as $condition){
                list($field,$operator,$compare) = explode(' ',$condition);
                if(!Utils::operate($row[$field],$operator,$compare)){
                    return false;
                }
            }
            return true;
        });

    }

    public function resultByID($recordID,$table, $id_field = 'id')
    {
        return isset($this->tables[$table]) && isset($this->tables[$table][$recordID]) ? $this->tables[$table][$recordID] : null;
    }
    
}
