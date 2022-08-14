<?php
namespace Core\Traits;

trait SQLUtils
{

    private static function whereQuery($conditions)
    {
        $wherecount = count($conditions);
        if($wherecount  > 0){
            $whereString = " WHERE ";
            $whereString .= (count($conditions) == 1) ? $conditions[0] : implode(' AND ',$conditions);
        }else{
            $whereString = "";
        }

        return $whereString;
    }

    public static function selectQuery($fields,$conditions,$table)
    {

        $whereString = self::whereQuery($conditions);

        $selectString = implode(', ',$fields);

        $query = "SELECT ".$selectString." FROM ".$table.$whereString;

        return $query;
    }

    public static function insertQuery($record, $table)
    {
        $fields = implode(', ', array_keys($record));
        $values = "'".implode("', '", array_values($record))."'";
        $query = "INSERT INTO $table ($fields) VALUES ($values);";
        return $query;
    }

    public static function updateQuery($record,$conditions, $table)
    {
        $whereString = self::whereQuery($conditions);

        $set = [];
        foreach($record as $field => $value){
            $set[] = "$field = '$value'";
        }

        $setString = implode(", ",$set);

        $query = "UPDATE $table SET ".$setString.$whereString;

        return $query;
    }

    public static function deleteQuery($conditions,$table){
        
        $whereString = self::whereQuery($conditions);

        $query = "DELETE FROM ".$table.$whereString;

        return $query;
    }
}