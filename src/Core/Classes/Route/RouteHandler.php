<?php

namespace Core\Classes\Route;

use Core\Classes\View;

final class RouteHandler
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PARAMETER_PATTERN = '[a-zA-Z0-9\_\-\.]+';

    public const METHODS = [
        RouteHandler::GET,
        RouteHandler::POST
    ];

    /**
     * @var string
     */
    private string $id;
    /**
     * @var string
     */
    private string $uri;
    /**
     * @var string
     */
    private string $method =  '';
    /**
     * @var RouteCallback
     */
    private RouteCallback $callback;
    /**
     * @var array<mixed>
     */
    private array $parameters = [];

    /**
     * @var View|null
     */
    private ?View $view = null;

    /**
     * @param string $uri
     * @param array<mixed>|string|callable $callback
     * @param value-of<RouteHandler::METHODS> $method
     */
    public function __construct(string $uri, string|array|callable|RouteCallback|View  $callback = '', string $method = RouteHandler::GET)
    {
        if (empty($uri)) {
            throw new \InvalidArgumentException("The route cannot be an empty string", 1);
        }

        $this->uri = $uri;

        $this->callback = $callback instanceof RouteCallback ? $callback : new RouteCallback($callback);

        if (!in_array($method, self::METHODS)) {
            throw new \InvalidArgumentException("Method parameter should be ".implode('|', self::METHODS).", '$method' given", 1);
        }
        $this->method = $method;

        $this->id = self::getURIPattern($this->uri)['path'];

        $this->parameters = self::getURIPattern($this->uri)['parameters'];
    }

    /**
     * @return string
     */
    public function getBaseURI(): string
    {
        return $this->uri;
    }

    /**
     * @return array<mixed>|callable
     */
    public function getCallback(): RouteCallback
    {
        return $this->callback;
    }

    /**
     * @param mixed $args
     *
     * @return mixed
     */
    public function callback(mixed $args = []): mixed
    {
        return ($this->getCallback())($args);
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array<mixed>
     */
    public function getURIParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param string $uri
     *
     * @return array{path:string, parameters:array<string>}
     */
    private static function getURIPattern(string $uri): array
    {
        $uriParts = explode('/', $uri);
        $parameters = [];
        $path = '';

        foreach ($uriParts as $index => $part) {
            if (empty($part)) {
                continue;
            }
            if (preg_match('/:[a-zA-Z0-9\_\-]+:?|\{[a-zA-Z0-9\_\-]+\}/', $part)) {
                $parameters[$index] = str_replace([':','{','}'], '', $part);
                $path .= preg_quote('/', '/'). self::PARAMETER_PATTERN;
            } else {
                $path .= preg_quote('/'.$part, '/');
            }
        }
        return ['path' => empty($path) ? preg_quote('/', '/') : $path, 'parameters' => $parameters];
    }

    /**
     * @param array<mixed> $routeArray
     *
     * @return RouteHandler
     */
    private static function fromArray(array $routeArray): RouteHandler
    {
        if (empty($routeArray) || (!isset($routeArray['uri']) && !is_string($routeArray[0]))) {
            throw new \InvalidArgumentException("The array should at least have a string value as a URI, in the 'uri' or 0 index", 1);
        }

        if (isset($routeArray['uri'])) {
            $uri = $routeArray['uri'];
            $callback = isset($routeArray['callback']) ? $routeArray['callback'] : '';
            $method = isset($routeArray['method']) ? $routeArray['method'] : self::GET;
        } else {
            $uri = array_shift($routeArray);
            $callback =  !empty($routeArray) ? array_shift($routeArray) : '';
            $method =  !empty($routeArray) ? array_shift($routeArray) : self::GET;
        }

        return new self($uri, $callback, $method);
    }

    /**
     * @param string $uri
     *
     * @return array<mixed>|false
     */
    public function getParametersValues(string $uri): array|false
    {
        if (empty($this->parameters)) {
            return false;
        }

        $uriParts = explode('/', explode('?', $uri)[0]);
        $values = [];

        foreach ($uriParts as $index => $part) {
            if (empty($part) || !array_key_exists($index, $this->parameters)) {
                continue;
            }
            $values[  $this->parameters[$index] ] = $part;
        }

        return $values;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $uri
     *
     * @return RouteHandler
     */
    private static function fromString(string $uri): RouteHandler
    {
        return new self($uri);
    }

    /**
     * @param string|array<mixed>|RouteHandler $route
     *
     * @return RouteHandler
     */
    public static function create(string|array|RouteHandler $route): RouteHandler
    {
        if ($route instanceof RouteHandler) {
            return $route;
        }

        if (is_array($route)) {
            return self::fromArray($route);
        }

        return self::fromString($route);
    }

    public static function notFoundRoute(): RouteHandler
    {
        return new self('/404', 'Not Found');
    }

    public function view($path, $layout = null)
    {
        $this->view = (new View($path))->layout($layout);
        return $this;
    }

    public function getView()
    {
        return $this->view;
    }
}
