<?php

namespace Models;

use Core\Classes\ModelRepository;

class StockTransactionRepository extends ModelRepository
{
    protected string $table = 'stock_transaction';

    protected array $fields = ['amount', 'observation', 'date', 'product_id'];

    protected string $modelClass = StockTransaction::class;
    
    /**
     * @param StockTransaction $stockAdjustment
     *
     * @return void
     */
    public function updateStock(ProductRepository $productRepository , StockTransaction $stockAdjustment): void
    {
        $product = $productRepository->find($stockAdjustment->getProductID());
        $product->setValue('stock',   $product->getValue('stock') + $stockAdjustment->getAmount());
        $productRepository->update($product);
        $this->insert($stockAdjustment);
    }
}