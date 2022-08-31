<?php


declare(strict_types=1);

use Core\Traits\QueryBuilder;
use PHPUnit\Framework\TestCase;

final class QueryBuilderTest extends TestCase
{
    public function test_initial_where_query(): void
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder->where('id',1)->getQuery();

        $this->assertSame("id = '1'",$result);
    }

    public function test_where_query_chain()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder->where('id',1)
        ->where('age','>',18)
        ->getQuery();

        $this->assertSame("id = '1' AND age > '18'",$result);
    }

    public function test_where_query_chain_and_or()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder
        ->where('name','juan')
        ->and('age','=',5)
        ->or('age','>',10)

        ->getQuery();

        $this->assertSame("name = 'juan' AND age = '5' OR age > '10'",$result);
    }


    public function test_query_with_groups()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder
        ->where('name','juan')
        ->andGrp('age','=',5)
        ->or('age','=',8)
        ->or('age','>',10)
        ->getQuery();

        $this->assertSame("name = 'juan' AND ( age = '5' OR age = '8' OR age > '10' )",$result);
    }

    public function test_query_with_advanced_groups()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder
        ->where('name','Juan')
        ->and('lastname','García')
        ->andGrp('age','=',5)
        ->orGrp('country','Italy')
        ->and('country','Germany')
        ->closeGrp()
        ->or('profession','developer')
        ->getQuery();

        $this->assertSame("name = 'Juan' AND lastname = 'García' AND ( age = '5' OR ( country = 'Italy' AND country = 'Germany' ) OR profession = 'developer' )",$result);
    }

    public function test_query_with_not_and_not_groups()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder
        ->notGrp('a','b')
        ->or('c','d')
        ->closeGrp()
        ->and('e','f')
        ->not('h','g')
        ->andNot('i','j')
        ->getQuery();

        $this->assertSame("NOT ( a = 'b' OR c = 'd' ) AND e = 'f' AND NOT ( h = 'g' ) AND NOT ( i = 'j' )",$result);        
    }

    public function test_query_for_stock_table()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder
        ->where('name','like','%Apple%')
        ->or('presentation','like','%can')
        ->getQuery();

        $this->assertSame("name LIKE '%Apple%' OR presentation LIKE '%can'",$result);        
    }


    public function test_order_query()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder->orderBy('ID')->getOrderQuery();

        $this->assertSame("ORDER BY ID",$result);

        $result = $queryBuilder->orderBy('name','desc')->getOrderQuery();

        $this->assertSame("ORDER BY ID, name DESC",$result);

        $result = $queryBuilder->orderAscBy('CITY')->getOrderQuery();

        $this->assertSame("ORDER BY ID, name DESC, CITY ASC",$result);


    }

    public function test_where_query_with_where_keyword()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $result = $queryBuilder
        ->where('name','juan')
        ->and('age','=',5)
        ->or('age','>',10)

        ->getWhereQuery();

        $this->assertSame("WHERE name = 'juan' AND age = '5' OR age > '10'",$result);
    }

    public function test_where_and_order_query()
    {
        $queryBuilder = $this->getObjectForTrait(QueryBuilder::class);

        $queryBuilder
        ->where('name','juan')
        ->and('age','=',5)
        ->or('age','>',10)
        ->orderBy('name')
        ->orderDescBy('age');
        
        $result = $queryBuilder->getWhereQuery();
        $result .= ' '.$queryBuilder->getOrderQuery();

        $this->assertSame("WHERE name = 'juan' AND age = '5' OR age > '10' ORDER BY name, age DESC",$result);
    }


}
