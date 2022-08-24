<?php

use Core\Classes\App;
use Core\Classes\ConfigManager;
use Core\Classes\Route\Router;
use Core\Classes\View;

function app(): App
{
    return App::getInstance();
}

function router(): Router
{
    return Router::getInstance();
}

function request(string $name = null): mixed
{
    $parameters = router()->getRequestParameters();    
    if (is_null($name)) {
        return $parameters;
    }
    return router()->getRequestParameter($name);
}

function redirect($uri)
{
    Router::getInstance()->redirect($uri);
}

function view($name, array $args = null)
{
    if(!is_null($args))
        return (new View($name))->render($args);    
    return (new View($name));
}

function config()
{
   return ConfigManager::getInstance();
}

function configdir($dir)
{
    return config()->dir($dir);
}

/**
 * Concrete application start. 
 * @return void
 */
function runApp(): void
{
    router()->clearRoutePool();
    router()->registerRoutes(arrayFromFile(path(configdir('config'), 'routes.php')));    
}
