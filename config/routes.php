<?php

router()->setNotFoundRoute(['/404',view('404')]);
return [
    ['/',view('index')],
    ['/',view('index'),'POST'],
    ['/home',view('index'),'GET'],   
    ['/home',view('index'),'POST'],
    ['/product/','ProductController@index'],
    ['/product/create',view('product/create')],
    ['/product/:id',[ProductController::class,'show']],
    ['/product/edit/:id','ProductController@edit'],
    ['/product/store/','ProductController@store','POST'],
    ['/product/delete/:id','ProductController@destroy','POST'],
    
];
