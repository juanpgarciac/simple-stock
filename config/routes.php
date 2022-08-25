<?php

router()->setNotFoundRoute(['/404',view('404')]);
return [
    ['/',view('index')->layout('layouts/main')],
    ['/',view('index'),'POST'],
    ['/home',view('index')->layout('layouts/main'),'GET'],   
    ['/home',view('index'),'POST'],
    ['/product/','ProductController@index'],
    ['/product/:id',[ProductController::class,'show']],
    ['/product/create/','ProductController@edit'],
    ['/product/edit/:id:','ProductController@edit'],
    ['/product/store/','ProductController@store','POST'],
    ['/product/delete/{id}','ProductController@destroy','POST'],
    
];
