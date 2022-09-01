<?php 

namespace Core\Traits\QueryBuilder\Segments;

trait SelectQueryBuilder
{
    private $selectArray = [];
    private $fromArray = [];

    public function select(array|string $fields = '*'): static
    {
        if (!is_array($fields)) {
            array_push($this->selectArray, ...explode(',',$fields));
        }else{
            array_push($this->selectArray, ...$fields);
        }
        return $this;
    }

    public function getSelectQuery():string
    {
        $arr = !empty($this->getSelectQueryArray()) ? $this->getSelectQueryArray() : ['*'];
        return 'SELECT '.trim(implode(' ',$arr));        
    }

    public function getSelectQueryArray():array
    {
        return $this->selectArray;
    }

    private function clearSelectQuery():void
    {
        $this->selectArray = [];
    }

}