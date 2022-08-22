<?php

namespace Core\Classes;

use ReflectionFunction;

final class Router extends Singleton
{
    private static ?Router $instance = null;

    /**
     * @var array<mixed>
     */
    private array $routePool = [];

    private RouteHandler $notFoundRoute;

    private function __construct()
    {
        $this->clearRoutePool();
        $this->notFoundRoute= RouteHandler::notFoundRoute();
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
        $routeHandler = RouteHandler::create($route);
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
        $uri = explode('?',$uri)[0];
        foreach($this->routePool as $methodKey => $methodRoutePool){
            if($method !== null  && $method !== $methodKey){                
                continue;
            }

            foreach ($methodRoutePool as $uri_pattern => $route) {  
                if(preg_match("#^$uri_pattern$#",$uri)){    
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

    public function route($uri, $method = RouteHandler::GET, $requestData = null)
    {
        $route = $this->getRouteByURI($uri, $method);
        if($route === false){
            http_response_code(404);
            $route = $this->getNotFoundRoute();
        }

        $uriParameters = $route->getParametersValues($uri);

        if($uriParameters === false){
            if($requestData === null)
                return $route->callback();
            return $route->callback($requestData);
        }else{
            if(empty($requestData))
                return $route->callback($uriParameters);
            return $route->callback(array_merge($uriParameters, ['request' => $requestData]));
        }

        /* *
        

        $callbackReflection = new ReflectionFunction($route->getCallback());
        $callbackParameters = $callbackReflection->getParameters();


        if($parameters === false){
              
        }

        if(is_null($request))
            return $route->callback(extract($parameters));
        return $route->callback($request, extract($parameters));

        return $this->notFoundRoute->callback($request);
        /* */
    }

    public function setNotFoundRoute(array|string|RouteHandler $route)
    {
        $this->notFoundRoute = RouteHandler::create($route);
    }

    public function getNotFoundRoute():RouteHandler
    {
        return $this->notFoundRoute;
    }

    public function routeWithServerVars()
    {
        $this->route($_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD'], $GLOBALS[ '_'.$_SERVER['REQUEST_METHOD'] ]);
    }
}
