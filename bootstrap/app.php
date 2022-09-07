<?php

/** Application helper functions */

use Core\Classes\App;
use Core\Classes\ConfigManager;
use Core\Classes\Route\RouteHandler as Route;
use Core\Classes\Route\Router;
use Core\Classes\View;
use Core\Command\PleaseCommand;

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
    router()::redirect($uri);
}

function back(mixed $args = [])
{
    router()::back($args);
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

function slot($descriptor, $args = []){
    $descriptor = str_replace('.','/',$descriptor);
    $supportedExtensions = ['php','html','phtml'];
    foreach ($supportedExtensions as $extension) {
        $path = path(configdir('views'), $descriptor.'.'.$extension);
        if(is_file($path)){
            extract($args);
            include $path;
            break;
        }
    }
}

function please():PleaseCommand
{
   return PleaseCommand::getInstance();
}

/**
 * Concrete application start. 
 * @return void
 */
function runApp(): void
{
    router()->clearRoutePool();
    router()->registerRoutes(arrayFromFile(path(configdir('config'), 'routes.php')));    
    please()->registerCommands(arrayFromFile(path(configdir('config'), 'commands.php')));    
}


/**
 * Concrete server start.
 * @return void
 */
function runServer():void
{
    router()->listenServer();
}