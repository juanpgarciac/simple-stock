<?php

use Core\Classes\App;
use Core\Classes\Router;

function app(): App
{
    return App::getInstance();
}

function router(): Router
{
    return app()->getRouter();
}
