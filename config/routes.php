<?php

router()->setNotFoundRoute(['/404',view('404')]);
return [
    ['/',view('index')],
    ['/',view('index'),'POST'],
    ['/home',view('index'),'GET'],   
    ['/home',view('index'),'POST'],
    ['/product/','ProductController@index'],
    ['/product/create',view('product/create')],
    ['/product/edit/:id','ProductController@edit'],
    ['/product','ProductController@store','POST'],
    ['/product/:id',[ProductController::class,'show']]
];
