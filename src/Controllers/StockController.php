<?php

namespace Controllers;

use Core\Classes\Controller;
use Models\ProductRepository;
use Models\StockTransaction;
use Models\StockTransactionRepository;

class StockController extends Controller
{
    public function adjustStock()
    {
        $productRepository = new ProductRepository(app()->getAppStorage());
        $stockTransaction = StockTransaction::fromArray(request());
        $stockRepository = new StockTransactionRepository(app()->getAppStorage());
        $stockRepository->updateStock($productRepository, $stockTransaction, request('transactionType'));

        redirect('/product/'.request('product_id').'?message=Stock updated ');
    }
}
