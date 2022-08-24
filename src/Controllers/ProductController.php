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
    public function index():void
    {
        $products =((new ProductRepository(app()->getAppStorage()))->results());
        view('/product/index')->with(compact('products'))->render();
    }

    /**
     * @return void
     */
    public function show(string $id)
    {
        $product = (new ProductRepository(app()->getAppStorage()))->find($id);
        if(is_null($product)){
            redirect('/product');
        }
        $product = $product->toArray();
        view('product/show')->with(compact('product'))->render();
    }

    /**
     * @return void
     */
    public function edit(string $id)
    {
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

        $product = ProductRepository::fromState(request());

        $message = 'Awesome!!! Product ';
        if($product->id()){
            $productRepository->update($product);
            $message .= 'Updated';
        }else{
            $productRepository->insert($product);
            $message .= 'Saved';            
        }

        view('/product/create')
        ->with($product->toArray())
        ->with(compact('message'))
        ->render();
   
    }

    public function destroy($id)
    {
        $productRepository = new ProductRepository(app()->getAppStorage());
        $productRepository->delete($id);
        redirect('/product');
    }
}
