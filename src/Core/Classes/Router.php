<?php

namespace Core\Classes;

final class Router extends Singleton
{
    private static ?Router $instance = null;

    private array $routePool = [];

    public static function getInstance(): Router
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function registerRoutes(array $routes): void
    {
        foreach($routes as $route)
        {
            $this->registerRoute($route);
        }
    } 

    public function registerRoute(array|string $route):void
    {
        $this->routePool[] = $route;
    } 

    public function getRoutePool()
    {
        return $this->routePool;
    }

    public function clearRoutePool()
    {
        $this->routePool = [];
    }

    
}
