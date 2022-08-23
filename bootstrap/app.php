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

function request(string $key = null): mixed
{
    $parameters = Router::getInstance()->getRequestParameters();
    if (is_null($key)) {
        return $parameters;
    }
    return isset($parameters[$key]) ? $parameters[$key] : null;
}

/**
 * I
 * @return void
 */
function runApp(): void
{
    router()->clearRoutePool();
    router()->registerRoutes(arrayFromFile(path(CONFIGDIR, 'routes.php')));
}
