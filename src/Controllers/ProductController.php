<?php

namespace Controllers;

use Core\Classes\Controller;
use Models\StockTransaction;
use Models\CategoryRepository;
use Models\Product;
use Models\ProductRepository;
use Models\StockTransactionRepository;
use Models\UnitRepository;

class ProductController extends Controller
{
    private $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository(app()->getAppStorage());
    }
    /**
     * @return void
     */
    public function index()
    {
        $this->productRepository->select('product.*, unit.unit, c.category, p.category parent')
        ->leftJoin('category c', 'product.category_id', 'c.id')
        ->leftJoin('category p', 'c.parent_id', 'p.id')
        ->leftJoin('unit', 'product.unit_id', 'unit.id');

        $search = trim(request('search'));
        if (!empty($search)) {
            foreach (['name','presentation','c.category','p.category'] as $field) {
                $this->productRepository->orWhere($field, 'like', "%$search%");
            }
        }

        $products = $this->productRepository->results();

        return compact('products');
    }

    /**
     * @return void
     */
    public function show(string $id)
    {
        $product =  $this->productRepository
        ->select('product.*, unit.unit, category.category')
        ->leftJoin('category', 'product.category_id', 'category.id')
        ->leftJoin('unit', 'product.unit_id', 'unit.id')
        ->find($id);

        if (empty($product)) {
            redirect('/product');
        }

        $transactions = ((new StockTransactionRepository(app()->getAppStorage()))
        ->where('product_id', '=', $product->id())->orderDescBy('id')->results());

        //usort($transactions,fn($a, $b)=> ($a['id'] < $b['id']));

        $product = $product->toArray();
        return compact('product', 'transactions');
    }

    /**
     * @return void
     */
    public function edit(string $id = null)
    {
        $product = null;
        if (!is_null($id)) {
            $product = $this->productRepository->find($id);
            if (is_null($product)) {
                redirect('/product');
            }
        }
        $categories = (new CategoryRepository(app()->getAppStorage()))->orderBy('category')->results();
        $units = (new UnitRepository(app()->getAppStorage()))->orderBy('unit')->results();
        view('/product/create')
        ->with($product?->toArray() ?? [])
        ->with(compact('categories', 'units'))
        ->layout('layouts/main')
        ->render();
    }

    public function store()
    {
        $productRepository = $this->productRepository;

        $product = Product::create(request());
        $message = 'Product ';
        if ($product->id()) {
            $product = $productRepository->update($product);
            $message .= 'Updated';
        } else {
            $product = $productRepository->insert($product);
            $message .= 'Saved';
            $stockTransaction = new StockTransaction($product->id(), request('initialStock'), 'Initial Stock');
            $stockRepository = new StockTransactionRepository(app()->getAppStorage());
            $stockRepository->updateStock($productRepository, $stockTransaction);
        }

        redirect('/product?message='.$message);
    }

    public function destroy($id)
    {
        $this->productRepository->delete($id);
        redirect("/product?message=Product $id deleted");
    }
}
