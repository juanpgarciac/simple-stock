<?php

use Controllers\ProductController;
use Core\Classes\FakeClass;

return [
    ['/',function(){  include_once path(VIEWSDIR,'/index.php');  }],
    ['/home',function(){  include_once path(VIEWSDIR,'/index.php'); }],
    ['/product',[ProductController::class,'index']],//['/product', [ProductController::class,'index']],
    ['/product/:id','ProductController@show']

];