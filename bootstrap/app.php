<?php

use Core\Classes\App;
use Core\Classes\ConfigManager;
use Core\Classes\Router;
use Core\Classes\View;

function app(): App
{
    return App::getInstance();
}

function router(): Router
{
    return Router::getInstance();
}

function request(string $key = null): mixed
{
    $parameters = Router::getInstance()->getRequestParameters();    
    if (is_null($key)) {
        return $parameters;
    }
    $method = Router::getInstance()->getRequestMethod();
    return isset($parameters[$key]) ? $parameters[$key] : (isset($parameters[$method]) ? $parameters[$method] : null);
}

function view($name)
{
    return (new View($name));
}

function config()
{
   return ConfigManager::getInstance();
}

/**
 * I
 * @return void
 */
function runApp(): void
{
    router()->clearRoutePool();
    router()->registerRoutes(arrayFromFile(path(config()->dir('config'), 'routes.php')));    
}
