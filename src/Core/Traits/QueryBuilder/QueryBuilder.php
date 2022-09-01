<?php 

namespace Core\Traits\QueryBuilder;

trait QueryBuilder
{
    use Segments\JoinQueryBuilder;
    use Segments\WhereQueryBuilder;
    use Segments\OrderQueryBuilder;

    /**
     * @return void
     */
    public function clearQuery():void
    {
        $this->clearWhereQuery();
        $this->clearJoinQuery();
        $this->clearOrderQuery();
    }

    /**
     * @return string
     */
    public function getQuery():string
    {
        return trim(implode(' ',
            [
                $this->getJoinQuery(),
                $this->getWhereQuery(),
                $this->getOrderQuery()
            ]
        ));
    }
}