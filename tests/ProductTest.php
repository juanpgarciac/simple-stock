<?php 

declare(strict_types=1);

use Core\DBConfig;
use Core\MySQL;
use Core\FakeDB;
use Models\Product;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{

    /** @test */
    public function Product_can_be_created()
    {
        $this->assertInstanceOf(Product::class, new Product);
    }

    /** @test */
    public function test_product_can_create_query()
    {
        $product = new Product(new FakeDB());
        $resultQuery = $product->select()->where('id','=','1')->query();

        $this->assertSame("SELECT * FROM product WHERE id = 1",$resultQuery);
    }
    
    /** @test */
    public function can_insert_product()
    {
        $DBconfig = DBConfig::FromEnvFile();

        //$product = new Product(new MySQL($DBconfig));
        $product = new Product(new FakeDB());

        $product->insert(['name'=>'Product1','presentation'=>'1','unit'=>'Lata','category'=>'Enlatados']);
        $product->insert(['name'=>'Product2','presentation'=>'1','unit'=>'Lata','category'=>'Enlatados']);
        $product->insert(['name'=>'Product3','presentation'=>'1','unit'=>'Lata','category'=>'Enlatados']);

        $this->assertCount(3, $product->results());

        var_dump($product->results());
    }



    
}