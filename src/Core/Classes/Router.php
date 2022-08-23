<?php

namespace Core\Classes;

final class Router extends Singleton
{
    private static ?Router $instance = null;

    /**
     * @var array<mixed>
     */
    private array $routePool = [];

    /**
     * @var RouteHandler
     */
    private RouteHandler $notFoundRoute;

    /**
     * @var array<mixed>
     */
    private array $requestParameters = [];

    private function __construct()
    {
        $this->clearRoutePool();
        $this->notFoundRoute= RouteHandler::notFoundRoute();
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
     * @param array<mixed>|string|RouteHandler $route
     *
     * @return void
     */
    public function registerRoute(array|string|RouteHandler $route): void
    {
        $routeHandler = RouteHandler::create($route);
        $this->routePool[$routeHandler->getMethod()][$routeHandler->id()] = $routeHandler;
    }

    /**
     * @param array<mixed>|string|RouteHandler ...$routes
     *
     * @return void
     */
    public function add(array|string|RouteHandler ...$routes): void
    {
        foreach ($routes as $route) {
            $this->registerRoute($route);
        }
    }

    /**
     * @param value-of<RouteHandler::METHODS> $method
     *
     * @return array<RouteHandler>
     */
    public function getRoutePool(string $method = RouteHandler::GET): array
    {
        return $this->routePool[$method];
    }

    /**
     * @return void
     */
    public function clearRoutePool(): void
    {
        $this->routePool = [];
        foreach (RouteHandler::METHODS as $methodindex) {
            $this->routePool[$methodindex] = [];
        }
    }

    /**
     * @param string $uri
     * @param string|null $method
     *
     * @return RouteHandler|false
     */
    public function getRouteByURI(string $uri, ?string $method = null): RouteHandler|false
    {
        $uri = explode('?', $uri)[0];
        foreach ($this->routePool as $methodKey => $methodRoutePool) {
            if (!is_null($method)  && $method !== $methodKey) {
                continue;
            }

            foreach ($methodRoutePool as $uri_pattern => $route) {
                if (preg_match("#^$uri_pattern\/?$#", $uri)) {
                    return $route;
                }
            }
        }
        return false;
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
    public function route(string $uri, string $method = RouteHandler::GET, string|array $requestData = null): mixed
    {
        $route = $this->getRouteByURI($uri, $method);
        if ($route === false) {
            http_response_code(404);
            $route = $this->getNotFoundRoute();
        }

        $uriParameters = $route->getParametersValues($uri);

        if ($uriParameters === false) {
            if ($requestData === null) {
                return $this->doTheRequest($route);
            }
            return $this->doTheRequest($route, array_merge(
                is_array($requestData) ? $requestData : [$requestData],
                ['request' => $requestData ],
                ['_'.$route->getMethod() => $requestData],
            ));
        } else {
            if (empty($requestData)) {
                return $this->doTheRequest($route, $uriParameters);
            }
            return $this->doTheRequest($route, array_merge(
                is_array($requestData) ? $requestData : [$requestData],
                ['request' => $requestData ],
                $uriParameters,
                ['_'.$route->getMethod() => $requestData],
            ));
        }
    }

    /**
     * @param RouteHandler $route
     * @param mixed|null $parameters
     *
     * @return mixed
     */
    public function doTheRequest(RouteHandler $route, mixed $parameters = null): mixed
    {
        $parameters = is_null($parameters) ? [] : (is_array($parameters) ? $parameters : [$parameters]);
        $this->setRequestParameters($parameters);
        return $route->callback($parameters);
    }

    /**
     * @param mixed $data
     *
     * @return void
     */
    public function setRequestParameters(mixed $data): void
    {
        $this->requestParameters = $data;
    }

    /**
     * @return array<mixed>
     */
    public function getRequestParameters(): array
    {
        return $this->requestParameters;
    }

    /**
     * @param array<mixed>|string|RouteHandler $route
     *
     * @return void
     */
    public function setNotFoundRoute(array|string|RouteHandler $route): void
    {
        $this->notFoundRoute = RouteHandler::create($route);
    }

    /**
     * @return RouteHandler
     */
    public function getNotFoundRoute(): RouteHandler
    {
        return $this->notFoundRoute;
    }

    /**
     * @return mixed
     */
    public function listenServer(): mixed
    {
        return $this->route(
            $_SERVER['REQUEST_URI'],
            $_SERVER['REQUEST_METHOD'],
            $GLOBALS[ '_'.$_SERVER['REQUEST_METHOD'] ]
        );
    }
}
