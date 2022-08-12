<?php 

namespace Core;

use Exception;
use mysqli;
use UnexpectedValueException;

abstract class Model
{
    protected string $table = '';
    protected array $fields = [];
    protected ?IDBConnection $DB = null;
    protected ?array $select = ['*'];
    protected array $where = [];
    protected array $results = [];

    public function __construct(?IDBConnection $DB = null)
    {
        $this->DB = $DB;
    }

    public function select(Array|String $fields = '*')
    {
        if(!is_array($fields)){
            $this->select = explode(',',$fields);
        }
        return $this;
    }

    public function where($field, $operator, $compare)
    {

        if(!in_array($operator,['=','>','<','>=','<=','like'])){
            throw new Exception("Invalid comparisor operator", 1);
        }

        $this->where[] = "$field $operator $compare";

        return $this;
    }

    public function query()
    {
       return $this->DB::class::query($this->select,$this->where,$this->table);
    }

    public function results($cached = false)
    {
        if(!$this->DB){
            throw new Exception("No DB configured", 1);
        }

        if(!$cached || !empty($this->results)){
            $this->results = $this->DB->results($this->select,$this->where,$this->table);
        }
        return $this->results;
       
    }


    public function insert(array $values)
    {
        if(!$this->DB){
            throw new Exception("No DB configured", 1);
        }

        if(empty($values))
            throw new Exception("Empty set", 1);

        $this->DB->insert(array_keys($values), array_values($values), $this->table);

    }

}