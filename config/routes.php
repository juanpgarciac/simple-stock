<?php

router()->setNotFoundRoute(['/404',view('404')]);
return [
    ['/',view('index')],
    ['/',view('index'),'POST'],
    ['/home',view('index'),'GET'],   
    ['/home',view('index'),'POST'],
    /* *
    ['/product/','ProductController@index'],
    ['/product/:id',[ProductController::class,'show']]
    /* */
];
