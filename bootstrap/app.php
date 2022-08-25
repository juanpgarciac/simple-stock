<?php

use Core\Classes\App;
use Core\Classes\ConfigManager;
use Core\Classes\Route\RouteHandler as Route;
use Core\Classes\Route\Router;
use Core\Classes\View;

function app(): App
{
    return App::getInstance();
}

function router(): Router
{
    return app()->getRouter();
}

function route(string|array|Route $route): Route
{
    $routeInstance = Route::create($route);
    
    router()->add($routeInstance);

    return $routeInstance;
}

function get($uri, $handler = null): Route
{
    return route([$uri,$handler]);
}

function post($uri, $handler = null): Route
{
    return route([$uri, $handler ,Route::POST]);
}

function request(string $name = null): mixed
{
    $parameters = router()->getRequestParameters();    
    if (is_null($name)) {
        return $parameters;
    }
    return router()->getRequestParameter($name);
}

function redirect($uri):void
{
    router()->redirect($uri);
}

function view($name, array $args = null)
{
    if(!is_null($args))
        return (new View($name))->render($args);    
    return (new View($name));
}

function config(): ConfigManager
{
   return app()->getConfigManager();
}

function configdir($dir):string
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
