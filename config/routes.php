<?php

use Controllers\CategoryController;

router()->setNotFoundRoute(['/404',view('404')]);

route(['/',view('index')->layout('layouts.main')]);

get('/home',view('index'));

get('/product/','ProductController@index')->view('product/index','layouts/main');

post('/product/store/','ProductController@store');

post('/product/delete/{id}','ProductController@destroy');

route(['/product/:id',[ProductController::class,'show']])->view('product/show');

post('/stock/adjust','StockController@adjustStock');

get('/category',[CategoryController::class,'index']);
get('/category/create',view('category/create'));
get('/category/edit/:id',[CategoryController::class,'edit'])->view('category/create');
post('/category/store',[CategoryController::class,'store']);
post('/category/delete/:id:',[CategoryController::class,'destroy']);



return [

    ['/product/create/','ProductController@edit'],

    ['/product/edit/:id:','ProductController@edit'],
    
];