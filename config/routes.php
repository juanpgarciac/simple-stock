<?php

use Controllers\ProductController;

return [
    ['/',function () {
        include_once path(VIEWSDIR, '/index.php');
    }],
    ['/home',function () {
        include_once path(VIEWSDIR, '/index.php');
    }],
    ['/product/','ProductController@index'],
    ['/product/:id',[ProductController::class,'show']]

];
