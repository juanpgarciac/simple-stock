<?php

use Controllers\CategoryController;
use Controllers\UnitController;

router()->setNotFoundRoute(['/404',view('404')]);

route(['/',view('index')->layout('layouts.main')]);

get('/home',view('index'));

get('/product/','ProductController@index')->view('product/index','layouts/main');

post('/product/store/','ProductController@store');

post('/product/delete/{id}','ProductController@destroy');

route(['/product/:id',[ProductController::class,'show']])->view('product/show');

post('/stock/adjust','StockController@adjustStock');

get('/category',[CategoryController::class,'index']);
get('/category/create',[CategoryController::class,'edit'])->view('category/create');
get('/category/edit/:id',[CategoryController::class,'edit'])->view('category/create');
post('/category/store',[CategoryController::class,'store']);
post('/category/delete/:id:',[CategoryController::class,'destroy']);

get('/unit',[UnitController::class,'index']);
get('/unit/create',view('unit/create'));
get('/unit/edit/:id',[UnitController::class,'edit'])->view('unit/create');
post('/unit/store',[UnitController::class,'store']);
post('/unit/delete/:id:',[UnitController::class,'destroy']);


return [

    ['/product/create/','ProductController@edit'],

    ['/product/edit/:id:','ProductController@edit'],
    
];