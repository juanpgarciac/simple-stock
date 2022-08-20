<?php

use Core\Classes\App;
use Core\Classes\Router;

function app(): App
{
    return App::getInstance();
}

function router(): Router
{
    return Router::getInstance();
}

/**
 * I
 * @return void
 */
function runApp():void
{
    router()->clearRoutePool();
    router()->registerRoutes(arrayFromFile(path(CONFIGDIR,'routes.php')));    
}