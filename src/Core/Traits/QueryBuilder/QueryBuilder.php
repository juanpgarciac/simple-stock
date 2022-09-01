<?php 

namespace Core\Traits\QueryBuilder;

trait QueryBuilder
{
    use Segments\SelectQueryBuilder;
    use Segments\FromQueryBuilder;
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
        $this->clearSelectQuery();
        $this->clearFromQuery();
    }

    /**
     * @return string
     */
    public function getQuery():string
    {
        return trim(implode(' ',
            [
                $this->getSelectQuery(),
                $this->getFromQuery(),
                $this->getJoinQuery(),
                $this->getWhereQuery(),
                $this->getOrderQuery()
            ]
        ));
    }
}