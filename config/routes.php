<?php

router()->setNotFoundRoute(['/404',view('404')]);

route(['/',view('index')->layout('layouts/main')]);

get('/home',view('index')->layout('layouts/main'));

get('/product/','ProductController@index');

post('/product/store/','ProductController@store');

post('/product/delete/{id}','ProductController@destroy');


return [
    
    ['/product/','ProductController@index'],

    ['/product/:id',[ProductController::class,'show']],

    ['/product/create/','ProductController@edit'],

    ['/product/edit/:id:','ProductController@edit'],
    
];