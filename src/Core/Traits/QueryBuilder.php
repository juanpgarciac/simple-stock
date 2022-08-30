<?php 

namespace Core\Traits;

trait QueryBuilder
{

    private $queryArray = [];
    private $openGroupsCount = 0;

    private static function assambleBase(array $args)
    {
        $field = '';
        $operator = '=';
        $compareTo = null;

        switch (count($args)) {
            case 0:
                //throw
                break;
            case 1: 
                $field = $args[0]; 
                break;
            case 2: 
                list($field, $compareTo) = array_slice($args,0,2);
                break;
            default:
                list($field, $operator, $compareTo) = [$args[0], $args[1], implode('',array_slice($args,2))];
                break;
        }

        $operator = strtoupper($operator);
        if (!in_array($operator, ['=','>','<','>=','<=','LIKE','<>','!='])) {
            throw new \InvalidArgumentException("Invalid comparisor operator", 1);
        }

        $operator = $operator == '!=' ? '<>' : $operator;

        return [$field, $operator, $compareTo];
    }


    private function queryIsOpen()
    {        
        $isIt =  $this->queryIsEmpty() || in_array(end($this->queryArray),['AND','OR','(']);
        reset($this->queryArray);
        return $isIt;
    }

    private function queryIsEmpty()
    {
        return empty($this->queryArray);
    }

    private function addQueryItem(string $queryItem)
    {
        $this->queryArray[] = $queryItem;
    }

    private function addQueryOperator(string $operator)
    {
        $this->addQueryItem($operator);
    }

    private function addQueryCondition($args)
    {
        list($field, $operator, $compareTo) =  self::assambleBase($args);
        $this->addQueryItem("$field $operator '$compareTo'");
    }


    public function where(...$args)
    {        
        if(!$this->queryIsOpen()){
            $this->addQueryOperator('AND');
        }

        $this->addQueryCondition($args);

        return $this;
    }

    public function orWhere(...$args)
    {
        if(!$this->queryIsOpen()){
            $this->addQueryOperator('OR'); 
        }
        $this->addQueryCondition($args);

        return $this;

    }

    public function whereNot(...$args)
    {

        if(!$this->queryIsOpen()){
            $this->addQueryOperator('AND');
        }

        $this->addQueryOperator('NOT'); 
        $this->startGroup();
        $this->addQueryCondition($args);
        $this->closeGrp();
        return $this;

    }

    public function and(...$args)
    {
        return $this->where(...$args);        
    }

    public function or(...$args)
    {
        return $this->orWhere(...$args);        
    }

    public function not(...$args)
    {
        return $this->whereNot(...$args);
    }

    public function andNot(...$args)
    {
        if(!$this->queryIsOpen()){
            $this->addQueryOperator('AND');
        }
        return $this->whereNot(...$args);        
    }

    public function orNot(...$args)
    {
        if(!$this->queryIsOpen()){
            $this->addQueryOperator('OR');
        }
        return  $this->whereNot(...$args);
    }

    private function startGroup()
    {
        $this->addQueryItem("(");
        $this->incrementGroupCount();
    }
    private function finishGroup()
    {
        if($this->openGroupsCount > 0){
            $this->addQueryItem(")");
            $this->decrementGroupCount();
        }            
    }

    public function andGrp(...$args)
    {
        if(!$this->queryIsOpen())
            $this->addQueryOperator('AND');
        $this->startGroup();
        $this->and(...$args);
        return $this;
    }

    public function orGrp(...$args)
    {
        if(!$this->queryIsOpen())
            $this->addQueryOperator('OR');
        $this->startGroup();
        $this->or(...$args);       
        return $this;
    }

    public function notGrp(...$args)
    {
        if(!$this->queryIsOpen()){
            $this->addQueryOperator('AND');
        }
        $this->addQueryOperator('NOT'); 
        $this->startGroup();
        $this->where(...$args);
        return $this;
    }

    public function closeGrp()
    {
        $this->finishGroup();
        return $this;
    }

    public function closeGrps():self
    {
        while($this->openGroupsCount > 0)
            $this->closeGrp();
        return $this;
    }

    /**
     * @return void
     */
    private function incrementGroupCount():void
    {
        $this->openGroupsCount++;
    }

    /**
     * @return void
     */
    private function decrementGroupCount():void
    {
        if($this->openGroupsCount > 0)
            $this->openGroupsCount--;
    }

    /**
     * @return string
     */
    public function getQuery():string
    {
        $this->closeGrps();
        return implode(' ',$this->queryArray);
    }
}