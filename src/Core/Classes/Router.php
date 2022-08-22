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

    private array $requestParameters = [];

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

    public function add(array|string|RouteHandler ...$routes):void
    {

        foreach ($routes as $route) 
            $this->registerRoute($route);
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
                if(preg_match("#^$uri_pattern\/?$#",$uri)){    
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
                return $this->doTheRequest($route);
            return $this->doTheRequest($route, array_merge(  
                is_array($requestData) ? $requestData : [$requestData],
                ['request' => $requestData ],
                ['_'.$route->getMethod() => $requestData], 
            ));
        }else{
            if(empty($requestData))
                return $this->doTheRequest($route, $uriParameters);
            return $this->doTheRequest($route, array_merge( 
                is_array($requestData) ? $requestData : [$requestData],
                ['request' => $requestData ],
                $uriParameters,
                ['_'.$route->getMethod() => $requestData], 
            ));
        }
    }

    public function doTheRequest(RouteHandler $route, mixed $parameters = null)
    {
        $parameters = is_null($parameters) ? [] : (is_array($parameters) ? $parameters : [$parameters] );
        $this->setRequestParameters($parameters);
        return $route->callback($parameters);
    }

    public function setRequestParameters(mixed $data):void
    {
        $this->requestParameters = $data;
    }

    public function getRequestParameters()
    {
        return $this->requestParameters;
    }

    public function setNotFoundRoute(array|string|RouteHandler $route)
    {
        $this->notFoundRoute = RouteHandler::create($route);
    }

    public function getNotFoundRoute():RouteHandler
    {
        return $this->notFoundRoute;
    }

    public function listenServer()
    {
        $this->route(
            $_SERVER['REQUEST_URI'],
            $_SERVER['REQUEST_METHOD'], 
            $GLOBALS[ '_'.$_SERVER['REQUEST_METHOD'] ]
        );
    }
}
