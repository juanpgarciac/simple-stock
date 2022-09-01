<?php 

namespace Core\Traits\QueryBuilder\Segments;

trait FromQueryBuilder
{
    private $fromArray = [];

    public function from($from):static
    {
        if (!is_array($from)) {
            array_push($this->fromArray, ...explode(',',$from));
        }else{
            array_push($this->fromArray, ...$from);
        }
        return $this;
    }

    public function getFromQuery():string
    {
        if(!empty($this->getFromQueryArray()) )
            return 'FROM '.trim(implode(' ',$this->getFromQueryArray()));        
        return '';
    }

    public function getFromQueryArray():array
    {
        return $this->fromArray;
    }

    private function clearFromQuery():void
    {
        $this->fromArray = [];
    }

}