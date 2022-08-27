<?php

namespace Models;

use Core\Classes\ModelRepository;

class StockTransactionRepository extends ModelRepository
{
    protected string $table = 'stock_transaction';

    protected array $fields = ['amount', 'observation', 'date', 'product_id','stock'];

    protected string $modelClass = StockTransaction::class;
    
    public const ADD = 'add';
    public const SUBSTRACT = 'substract';
    public const SET = 'set';

    /**
     * @param StockTransaction $stockAdjustment
     *
     * @return void
     */
    public function updateStock(ProductRepository $productRepository , StockTransaction $stockAdjustment, $transactionType = Self::ADD): void
    {
        $product = $productRepository->find($stockAdjustment->getProductID());
        $productAmount = $product->getValue('stock');

        if($transactionType === self::SET){
            //If this is a SET (RESET) transaction we have to log the transaction that would take stock to 0.  
            $resetAmount =  -1 * $productAmount;
            $this->insert(new StockTransaction($product->id(), $resetAmount, "Reset $productAmount ".$product->getValue('unit')." Stock"));
            $amountToSet = $stockAdjustment->getAmount();
        }else{
            if($transactionType === self::SUBSTRACT) {
                $stockAdjustment->setValue('amount', -1 * abs( $stockAdjustment->getAmount()));
            }
            $amountToSet = $productAmount + $stockAdjustment->getAmount();
        }
        
        //log new stock value
        $stockAdjustment->setValue('stock', $amountToSet);
        if(empty($stockAdjustment->getObservation() )){
            $stockAdjustment->setValue('observation', ($stockAdjustment->getAmount() >= 0 ? "Add " : "Substract " ).abs($stockAdjustment->getAmount())." ".$product->getValue('unit'));   
        }

        //log the stock transaction
        if($this->insert($stockAdjustment)!== false){
            $product->setValue('stock', $amountToSet);//update product  with new stock value
            $productRepository->update($product);
        }
    }
}