<?php
namespace Core;

use Core\IDBConnection;

abstract class SQLBase implements IDBConnection
{
    public static function query($fields,$conditions,$table)
    {
        $wherecount = count($conditions);
        if($wherecount  > 0){
            $whereString = " WHERE ";
            $whereString .= (count($conditions) == 1) ? $conditions[0] : implode(' AND ',$conditions);
        }else{
            $whereString = "";
        }

        $selectString = implode(', ',$fields);

        $query = "SELECT ".$selectString." FROM ".$table.$whereString;

        return $query;
    }

    public static function insertQuery($fields, $values, $table)
    {
        $fields = implode(', ', $fields);
        $values = "'".implode("', '", $values)."'";
        $query = "INSERT INTO $table ($fields) VALUES ($values);";
        return $query;
    }
}