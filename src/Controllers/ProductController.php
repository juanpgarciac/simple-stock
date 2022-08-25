<?php

namespace Controllers;

use Core\Classes\Controller;
use Models\CategoryRepository;
use Models\Product;
use Models\ProductRepository;
use Models\UnitRepository;

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
    public function edit(string $id = null)
    {   $product = null;
        if(!is_null($id)){
            $product = (new ProductRepository(app()->getAppStorage()))->find($id);   
            if(is_null($product)){
                redirect('/product');
            }
        }
        $categories = (new CategoryRepository(app()->getAppStorage()))->results();     
        $units = (new UnitRepository(app()->getAppStorage()))->results();     
        view('/product/create')
        ->with($product?->toArray() ?? [])        
        ->with(compact('categories','units'))
        ->render();
    }

    public function store()
    {
        $productRepository = new ProductRepository(app()->getAppStorage());

        $product = Product::create(request());
        $message = 'Awesome!!! Product ';
        if($product->id()){
            $productRepository->update($product);
            $message .= 'Updated';
        }else{
            $productRepository->insert($product);
            $message .= 'Saved';            
        }

        redirect('/product?message='.$message);
   
    }

    public function destroy($id)
    {
        $productRepository = new ProductRepository(app()->getAppStorage());
        $productRepository->delete($id);
        redirect('/product');
    }
}
