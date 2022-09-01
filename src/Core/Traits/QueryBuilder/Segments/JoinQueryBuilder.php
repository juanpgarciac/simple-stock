<?php 

namespace Core\Traits\QueryBuilder\Segments;

trait JoinQueryBuilder
{
    private $joinArray = [];

    private function createJoin(string $joinType, string $joinTo, string $usingField, string $withField = null):static
    {
        $joinType = empty($joinType)? '':strtoupper($joinType);
        $joinType = !in_array($joinType, ['INNER','LEFT','RIGHT','FULL']) ? '' : $joinType;
        $withField = $withField ?? "`$joinTo`.`$usingField`";

        $this->joinArray[] = trim($joinType." JOIN $joinTo ON $usingField = $withField");
        return $this;
    }

    public function join(string $joinTo, string $usingField, string $withField = null):static
    {
        return $this->createJoin('',$joinTo,$usingField,$withField);
    }

    public function innerJoin(string $joinTo, string $usingField, string $withField = null):static
    {                
        return $this->createJoin('INNER', $joinTo, $usingField, $withField);
    }

    public function leftJoin(string $joinTo, string $usingField, string $withField = null):static
    {        
        return $this->createJoin('LEFT', $joinTo, $usingField, $withField);
    }

    public function rightJoin(string $joinTo, string $usingField, string $withField = null):static
    {        
        return $this->createJoin('RIGHT', $joinTo, $usingField, $withField);
    }

    public function fulljoin(string $joinTo, string $usingField, string $withField = null):static
    {        
        return $this->createJoin('FULL', $joinTo, $usingField, $withField);
    }

    public function getJoinQuery():string
    {
        return trim(implode(' ',$this->getJoinQueryArray()));
    }

    public function getJoinQueryArray():array
    {
        return $this->joinArray;
    }

    private function clearJoinQuery():void
    {
        $this->joinArray = [];
    }
}