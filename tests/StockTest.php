<?php


declare(strict_types=1);

use Core\Classes\DBConfiguration;
use Core\Classes\StorageDrivers\FakeDBDriver;
use Core\Interfaces\IStorageDriver;
use Models\Product;
use Models\ProductRepository;
use Models\StockTransaction;
use Models\StockTransactionRepository;
use PHPUnit\Framework\TestCase;

final class StockTest extends TestCase
{
    private StockTransactionRepository $stockRepository;
    private ProductRepository $productRepository;
    private IStorageDriver $storagedriver;
    private DBConfiguration $dbconfiguration;
    
    protected function setUp():void
    {
        parent::setUp();
        $this->dbconfiguration = app()->getDBConfiguration();
        $this->storagedriver = app()->getAppStorage();

        $this->productRepository = new ProductRepository($this->storagedriver);
        $this->stockRepository = new StockTransactionRepository($this->storagedriver);
    }

    public function test_instance_stock_model_and_repository(): void
    {
        $fakestorage = new FakeDBDriver();
        $stock = new StockTransaction();
        $stockRepository = new StockTransactionRepository($fakestorage);
        $this->assertInstanceOf(StockTransaction::class,$stock);
        $this->assertInstanceOf(StockTransactionRepository::class,$stockRepository);
    }  
    
    public function test_stockrepository_update_product_stock()
    {

        $product =  new Product(uniqid());

        
        $productRepository = $this->productRepository;
        $stockRepository = $this->stockRepository;

        $productRepository->deleteBatch(true);
        $stockRepository->deleteBatch(true);

        $product_id = $productRepository->insert($product)->id();

        $stockRepository->updateStock($productRepository,new StockTransaction($product_id,10));
        $stockRepository->updateStock($productRepository,new StockTransaction($product_id,20));
        $stockRepository->updateStock($productRepository,new StockTransaction($product_id,30));

        $this->assertCount(3,$stockRepository->results());

        $product = $productRepository->find($product_id);
        $this->assertSame((float)60, $product->getValue('stock'));

        $stockRepository->updateStock($productRepository,new StockTransaction($product_id,-10));
        $product = $productRepository->find($product_id);
        $this->assertSame((float)50, $product->getValue('stock'));

    }
}
