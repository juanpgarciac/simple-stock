<?php 

declare(strict_types=1);

use Core\Classes\DBConfiguration;
use Core\Classes\DBDrivers\FakeDB;
use Core\Classes\DBDrivers\MySQL;
use Models\ProductRepository;
use Models\Product;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{

    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $mySQL = new MySQL( DBConfiguration::FromEnvFile());
        $this->productRepository = new ProductRepository(  new FakeDB());
        //$this->productRepository = new ProductRepository(  $mySQL);
        
    }
    

    /** @test */
    public function Product_class_can_be_instanciated()
    {
        $this->assertInstanceOf(Product::class, new Product("Product Name"));
    }

    /** @test */
    public function test_product_can_create_mysql_query()
    {
        
        $this->productRepository->select()->where('id','=','1');

        $resultQuery = MySQL::selectQuery(
            $this->productRepository->getFieldSelection(),
            $this->productRepository->getConditions(),
            $this->productRepository->getTable());

        $this->assertSame("SELECT * FROM product WHERE id = '1'",$resultQuery);
    }
    
    /** @test */
    public function product_can_be_created()
    {

        $product = new Product('Tuna','1 Can','unit','cans');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('Tuna', $product->getValue('name'));
    }
    



    /** @test */
    public function product_can_be_inserted()
    {
        $randomName = 'Random Name '.date('Ymdhis');

        $product = new Product($randomName,'1 Can','unit','cans');
        
        $this->productRepository->insert($product);

        $results = $this->productRepository->where('name','=',$randomName)->results();

        $this->assertCount(1, $results);


        
        $productInserted =  $results[array_key_first($results)];

        $this->assertSame($randomName,$productInserted['name']);

        

    }

    /** @test */
    public function all_product_can_be_deleted()
    {
        $this->productRepository->deleteBatch(true); //override flag to delete without conditions (all records)

        $product = new Product('Tuna #1','1 Can','unit','cans');
        $this->productRepository->insert($product);

        $product = new Product('Tuna #2','1 Can','unit','cans');
        $this->productRepository->insert($product);

        $this->productRepository->deleteBatch(); //with no conditions will be all records, won't delete without override flag;

        $this->assertCount(2, $this->productRepository->results());

        $this->productRepository->deleteBatch(true); //override flag to delete without conditions (all records)

        $this->assertCount(0, $this->productRepository->results());
    }
    
    /** @test */
    public function product_can_be_retrieved()
    {
        
        $this->productRepository->deleteBatch(true); //override flag to delete without conditions (all records)

        $randomName = 'Random Name '.date('Ymdhis');

        $product = new Product($randomName,'1 Can','unit','cans');
        $this->productRepository->insert($product);

        $product = new Product('Tuna #1','1 Can','unit','cans');
        $this->productRepository->insert($product);

        $product = new Product('Tuna #2','1 Can','unit','cans');
        $this->productRepository->insert($product);


        $results = $this->productRepository->where('name','like',$randomName)->results();
        
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertSame($randomName, $results[array_key_first($results)]['name']);
        /* */

    }

    /** @test */
    public function product_can_be_retrieved_by_id()
    {
        $this->productRepository->deleteBatch(true);
     
        $randomName = 'Random Name '.date('Ymdhis');

        $product = new Product($randomName,'1 Can','unit','cans');
        $productAfterSave = $this->productRepository->insert($product);

        $productArr = $this->productRepository->find($productAfterSave->getValue('id'));

        $this->assertSame($randomName, $productArr['name']);




    }

    
}