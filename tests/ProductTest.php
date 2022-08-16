<?php


declare(strict_types=1);

use Core\Classes\DBConfiguration;
use Core\Classes\DBDrivers\FakeDBDriver;
use Core\Classes\DBDrivers\MySQLDriver;
use Core\Classes\DBDrivers\PostgreSQLDriver;
use Core\Classes\DBDrivers\SQLite3Driver;
use Models\ProductRepository;
use Models\Product;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        //$mySQL = new MySQLDriver( DBConfiguration::FromEnvFile());
        //$postgres = new PostgreSQLDriver(new DBConfiguration("quarantine_stock", 'debian', '12345', 'localhost', 5432));
        $sqlite = new SQLite3Driver(new DBConfiguration("/home/juanp/quarantine_stock.db"));

        //dd($sqlite->connect());
        //$this->productRepository = new ProductRepository(  new FakeDBDriver());
        //$this->productRepository = new ProductRepository(  $mySQL);
        //$this->productRepository = new ProductRepository($postgres);
        $this->productRepository = new ProductRepository($sqlite);
    }


    /** @test */
    public function Product_class_can_be_instanciated()
    {
        $this->assertInstanceOf(Product::class, new Product("Product Name"));
    }

    /** @test */
    public function test_product_can_create_mysql_query()
    {
        $this->productRepository->select()->where('id', '=', '1');

        $resultQuery = MySQLDriver::selectQuery(
            $this->productRepository->getFieldSelection(),
            $this->productRepository->getConditions(),
            $this->productRepository->getTable()
        );

        $this->assertSame("SELECT * FROM product WHERE id = '1'", $resultQuery);
    }

    /** @test */
    public function product_can_be_created()
    {
        $product = new Product('Tuna', '1 Can', 'unit', 'cans');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('Tuna', $product->getValue('name'));
    }




    /** @test */
    public function product_can_be_inserted()
    {
        $randomName = uniqid('Random Product Name ');

        $product = new Product($randomName, '1 Can', 'unit', 'cans');

        $this->productRepository->insert($product);

        $results = $this->productRepository->where('name', '=', $randomName)->results();

        $this->assertCount(1, $results);



        $productInserted =  $results[array_key_first($results)];

        $this->assertSame($randomName, $productInserted['name']);
    }


    /** @test */
    public function all_products_can_be_deleted()
    {
        $this->productRepository->deleteBatch(true); //override flag to delete without conditions (all records)

        $product = new Product('Tuna #1', '1 Can', 'unit', 'cans');
        $this->productRepository->insert($product);

        $product = new Product('Tuna #2', '1 Can', 'unit', 'cans');
        $this->productRepository->insert($product);

        $this->productRepository->deleteBatch(); //with no conditions will be all records, won't delete without override flag;

        $this->assertCount(2, $this->productRepository->results());

        $this->productRepository->deleteBatch(true); //override flag to delete without conditions (all records)

        $this->assertCount(0, $this->productRepository->results());
    }

    /** @test */
    public function product_can_be_deleted_by_id()
    {
        $product = new Product('Tuna #1', '1 Can', 'unit', 'cans');
        $this->productRepository->insert($product);

        $product = new Product('Tuna #2', '1 Can', 'unit', 'cans');
        $productAfterSave = $this->productRepository->insert($product);

        $id = $productAfterSave->id();
        $productFound = $this->productRepository->find($id);

        $this->assertEquals($id, $productFound->id());

        $this->productRepository->delete($id);

        $productFound = $this->productRepository->find($id);

        $this->assertNull($productFound);
    }

    /** @test */
    public function product_can_be_retrieved()
    {
        $this->productRepository->deleteBatch(true); //override flag to delete without conditions (all records)

        $randomName = uniqid('Random Product Name ');

        $product = new Product($randomName, '1 Can', 'unit', 'cans');
        $this->productRepository->insert($product);

        $product = new Product('Tuna #1', '1 Can', 'unit', 'cans');
        $this->productRepository->insert($product);

        $product = new Product('Tuna #2', '1 Can', 'unit', 'cans');
        $this->productRepository->insert($product);


        $results = $this->productRepository->where('name', 'like', $randomName)->results();

        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertSame($randomName, $results[array_key_first($results)]['name']);
        /* */
    }

    /** @test */
    public function product_can_be_retrieved_by_id()
    {
        $this->productRepository->deleteBatch(true);

        $randomName = uniqid('Random Product Name ');

        $product = new Product($randomName, '1 Can', 'unit', 'cans');
        $id = $this->productRepository->insert($product)->getValue('id');

        $productAfterSave = $this->productRepository->find($id);

        $this->assertEquals($randomName, $productAfterSave->getValue('name'));
    }

    /** @test */
    public function product_can_be_updated()
    {
        $randomName1 = uniqid('Random Product Name ');
        $randomName2 = uniqid('Random Product Name ');

        $product = new Product($randomName1, '1 Can', 'unit', 'cans');
        $productAfterInsert = $this->productRepository->insert($product);

        $productAfterInsert->setValue('name', $randomName2);

        $this->productRepository->update($productAfterInsert);

        $productAfterUpdate = $this->productRepository->find($productAfterInsert->id());

        $this->assertEquals($randomName2, $productAfterUpdate->getValue('name'));
    }
}
