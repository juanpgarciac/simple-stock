<?php
namespace Core;

use Core\SQLBase;

class FakeDB extends SQLBase
{

    private $tables = [];

    public function __construct(?DBConfig $DBConfig = null)
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
        return array_filter($this->tables[$table],function($row) use($fields,$conditions){

            return $row;


        });

    }

    public function insert($fields, $values, $table)
    {

        if(!isset($this->tables[$table] )){
            $this->tables[$table] = [];
        }
        $this->tables[$table][] = array_combine($fields,$values);

    }

    
}
