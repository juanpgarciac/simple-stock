<?php

namespace Controllers;

use Core\Classes\Controller;
use Models\Product;
use Models\ProductRepository;

class ProductController extends Controller
{
    /**
     * @return void
     */
    public function index(): void
    {
        echo "<h2>This is the Product index</h2>";
        dd((new ProductRepository(app()->getAppStorage()))->results());
    }

    /**
     * @return void
     */
    public function show(string $id): void
    {
        echo "<h2>This is the Product $id detail</h2>";
        dd((new ProductRepository(app()->getAppStorage()))->find($id));
    }

    /**
     * @return void
     */
    public function edit(string $id)
    {
        echo "<h2>This is the Product $id detail</h2>";

        $product = (new ProductRepository(app()->getAppStorage()))->find($id);        
        view('/product/create')
        ->with($product?->toArray() ?? [])
        ->with(['message'=>   is_null($product) ? 'Product '.$id.' doesn\'t exists' : ''])
        ->with(uniqid())
        ->render();
    }

    public function store()
    {
        $productRepository = new ProductRepository(app()->getAppStorage());
        $productRepository->insert(ProductRepository::fromState(request()));
        view('/product/create',['message'=>'Awesome!!! Product Saved']);
    }
}
