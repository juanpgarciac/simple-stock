<?php 

namespace Core\Traits\QueryBuilder\Segments;

trait OrderQueryBuilder
{
    private $orderArray = [];

    /**
     * @param string $field
     * @param string|null $sort
     * 
     * @return static
     */
    public function orderBy(string $field, string $sort = null):static
    {
        $sort = !empty($sort) ? trim(strtoupper($sort)) : '';
        $sort = !in_array($sort,['ASC','DESC']) ? '' : ' '.$sort;
        $this->orderArray[] = $field.$sort;
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return static
     */
    public function orderAscBy(string $field):static
    {
        $this->orderBy($field,'ASC');
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return static
     */
    public function orderDescBy(string $field):static
    {
        $this->orderBy($field,'DESC');
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderQuery():string
    {
        $arr = $this->getOrderQueryArray();
        if(empty($arr))
            return '';
        return 'ORDER BY '.trim(implode(', ',$this->getOrderQueryArray()));
    }

    /**
     * @return array
     */
    public function getOrderQueryArray():array
    {
        return $this->orderArray;
    }

    private function clearOrderQuery():void
    {
        $this->orderArray = [];
    }
}