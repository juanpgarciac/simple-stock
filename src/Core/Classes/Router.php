<?php

namespace Core\Classes;

final class Router extends Singleton
{
    private static ?Router $instance = null;

    /**
     * @var array<mixed>
     */
    private array $routePool = [];

    private function __construct()
    {
        $this->clearRoutePool();
    }

    public static function getInstance(): Router
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @param array<mixed> $routes
     * 
     * @return void
     */
    public function registerRoutes(array $routes): void
    {
        foreach($routes as $route)
        {
            $this->registerRoute($route);
        }
    } 

    public function registerRoute(array|string|RouteHandler $route):void
    {
        $routeHandler = $route instanceof RouteHandler?$route:RouteHandler::create($route);
        $this->routePool[$routeHandler->getMethod()][$routeHandler->id()] = $routeHandler;
    } 

    public function getRoutePool($method = RouteHandler::GET)
    {
        return $this->routePool[$method];
    }

    public function clearRoutePool()
    {
        $this->routePool = [];
        foreach(RouteHandler::METHODS as $methodindex)
            $this->routePool[$methodindex] = [];
    }

    public function getRouteByURI($uri, $method = null):RouteHandler|false
    {
        foreach($this->routePool as $methodKey => $methodRoutePool){
            if($method !== null  && $method !== $methodKey){                
                continue;
            }
                

            foreach ($methodRoutePool as $uri_pattern => $route) {               
                if(preg_match("/$uri_pattern/",$uri)){
                    return $route;
                }
            }
        }
        return false;
    }

    public function routeExists($uri, $method = null):bool
    {
        return $this->getRouteByURI($uri, $method) !== false;
    }
    
}
