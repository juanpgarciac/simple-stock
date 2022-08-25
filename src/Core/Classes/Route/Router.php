<?php

namespace Core\Classes\Route;

use Core\Classes\Singleton;
use Core\Classes\Route\RouteHandler as Route;

final class Router extends Singleton
{
    private static ?Router $instance = null;

    /**
     * @var array<mixed>
     */
    private array $routePool = [];

    /**
     * @var Route
     */
    private Route $notFoundRoute;

    /**
     * @var Request|null
     */
    private ?Request $currentRequest = null;

    private mixed $response = null;

    private function __construct()
    {
        $this->clearRoutePool();
        $this->notFoundRoute= Route::notFoundRoute();
    }

    /**
     * @return Router
     */
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
        foreach ($routes as $route) {
            $this->registerRoute($route);
        }
    }

    /**
     * @param array<mixed>|string|Route $route
     *
     * @return void
     */
    public function registerRoute(array|string|Route $route): void
    {
        $Route = Route::create($route);
        $this->routePool[$Route->getMethod()][$Route->id()] = $Route;
    }

    /**
     * @param array<mixed>|string|Route ...$routes
     *
     * @return void
     */
    public function add(array|string|Route ...$routes): void
    {
        foreach ($routes as $route) {
            $this->registerRoute($route);
        }
    }

    /**
     * @param value-of<Route::METHODS> $method
     *
     * @return array<Route>
     */
    public function getRoutePool(string $method = Route::GET): array
    {
        return $this->routePool[$method];
    }

    /**
     * @return void
     */
    public function clearRoutePool(): void
    {
        $this->routePool = [];
        foreach (Route::METHODS as $methodindex) {
            $this->routePool[$methodindex] = [];
        }
    }

    /**
     * @param string $uri
     * @param string|null $method
     *
     * @return Route|false
     */
    public function getRouteByURI(string $uri, ?string $method = null): Route|false
    {
        $candidates = [];
        $uri = explode('?', $uri)[0];
        foreach ($this->routePool as $methodKey => $methodRoutePool) {
            if (!is_null($method)  && $method !== $methodKey) {
                continue;
            }

            foreach ($methodRoutePool as $uri_pattern => $route) {
                if (preg_match("#^$uri_pattern\/?$#", $uri)) {    
                    //check if the route is a direct route                     
                    if(strtolower(str_replace('/','',$route->getBaseURI()) === strtolower(str_replace('/','',$uri))))
                        return $route;
                    $candidates[] = $route;//save candidate URL. 
                }
            }
        }
        //TODO we can have a criterian for best candidate to route. 
        return !empty($candidates) ? $candidates[0] : false;
    }

    /**
     * @param string $uri
     * @param null $method
     *
     * @return bool
     */
    public function routeExists(string $uri, string $method = null): bool
    {
        return $this->getRouteByURI($uri, $method) !== false;
    }


    /**
     * @param string $uri
     * @param string $method
     * @param array<mixed>|string|null $requestData
     *
     * @return mixed
     */
    public function route(string $uri, string $method = Route::GET, string|array $requestData = null): mixed
    {
        $route = $this->getRouteByURI($uri, $method);
        if ($route === false) {
            http_response_code(404);
            $route = $this->getNotFoundRoute();
        }

        $uriParameters = $route->getParametersValues($uri);

        $request = new Request($method,$requestData,$uriParameters);

        $this->response = $this->routeTheRequest($route,$request);

        if($route->getView())
            $this->response = $route->getView()->render($this->response,true);
        return $this->response;
    }

    /**
     * @param Route $route
     * @param mixed|null $parameters
     *
     * @return mixed
     */
    public function routeTheRequest(Route $route, Request $request): mixed
    {
        $this->currentRequest = $request;       
        return $route->callback($request->getParameters());
    }

    public function render()
    {
        echo $this->response;
    }

    /**
     * @return Request|null
     */
    public function getCurrentRequest(): Request|null
    {
        return $this->currentRequest;
    }

    /**
     * @return array<mixed>
     */
    public function getRequestParameters(): array
    {
        return $this->currentRequest?->getParameters() ?? [];
    }

    public function getRequestParameter(string $name):mixed
    {
        return $this->currentRequest?->getParameter($name) ?? null;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->currentRequest?->getMethod() ?? Route::GET;
    }

    /**
     * @param array<mixed>|string|Route $route
     *
     * @return void
     */
    public function setNotFoundRoute(array|string|Route $route): void
    {
        $this->notFoundRoute = Route::create($route);
    }

    /**
     * @return Route
     */
    public function getNotFoundRoute(): Route
    {
        return $this->notFoundRoute;
    }

    /**
     * @return mixed
     */
    public function listenServer(): mixed
    {
        $response = $this->route(
            $_SERVER['REQUEST_URI'],
            $_SERVER['REQUEST_METHOD'],
            $GLOBALS[ '_'.$_SERVER['REQUEST_METHOD'] ]
        );
        $this->render();
        return $response;
    }

    public static function redirect(string $uri):void
    {
        header("location: $uri",true,301);
        die;
    }
}
