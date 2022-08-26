<?php

router()->setNotFoundRoute(['/404',view('404')]);

route(['/',view('index')->layout('layouts.main')]);

get('/home',view('index'));

get('/product/','ProductController@index')->view('product/index','layouts/main');

post('/product/store/','ProductController@store');

post('/product/delete/{id}','ProductController@destroy');

route(['/product/:id',[ProductController::class,'show']])->view('product/show');

post('/stock/adjust','StockController@adjustStock');

return [

    ['/product/create/','ProductController@edit'],

    ['/product/edit/:id:','ProductController@edit'],
    
];