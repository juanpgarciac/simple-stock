<?php
namespace Core\Classes;

final class RouteHandler
{
    public const GET = 'GET';
    public const POST = 'POST';
    
    public const METHODS = [
        RouteHandler::GET,
        RouteHandler::POST
    ];


    private string $uri;
    private string $method =  '';
    private mixed $callback;
    
    /**
     * @param string $uri
     * @param string|callable $callback
     * @param value-of<RouteHandler::METHODS> $method
     */
    public function __construct(string $uri, string|callable $callback = '', string $method = RouteHandler::GET)    
    {
        if(empty($uri))
            throw new \InvalidArgumentException("The route cannot be an empty string", 1);

        $this->uri = $uri;

        if(is_callable($callback)){
            $this->callback = $callback;
        }else if(is_string($callback)){
            $this->callback =  function()use($callback){
                echo $callback;
            };
        }else{
            throw new \InvalidArgumentException("callback parameter should be string or callable", 1);
        }

        if(!in_array($method,self::METHODS))
            throw new \InvalidArgumentException("Method parameter should be ".implode('|',self::METHODS).", '$method' given", 1);
        $this->method = $method;
        
    }

    public function id():string
    {
        return self::getURIDynamicParameters($this->uri)['path'];
    }

    private static function getURIDynamicParameters(string $uri): array
    {
        $uriParts = explode('/',$uri);
        $parameters = [];
        $path = [];
        foreach ($uriParts as $index => $part) {
            if(str_starts_with($part,':')){
                $parameters[$index] = str_replace(':','',$part); 
                $path[] = '@';
            }else{
                $path[] = $part;
            }
        }
        return ['path' => implode('/',$path), 'parameters' => $parameters];
    }

    private static function fromArray(array $routeArray): RouteHandler
    {

        if(empty($routeArray) || (!isset($routeArray['uri']) && !is_string($routeArray[0]))){
            throw new \InvalidArgumentException("The array should at least have a string value as a route, in the 'route' or 0 index", 1);            
        }

        if(isset($routeArray['uri'])){
            $route = $routeArray['uri'];
            $callback = isset($routeArray['callback']) ? $routeArray['callback'] : '';
            $method = isset($routeArray['method']) ? $routeArray['method']: self::GET;

            
        }else{
            $uri = array_shift($routeArray);
            $callback =  !empty($routeArray) ? array_shift($routeArray) : '';
            $method =  !empty($routeArray) ? array_shift($routeArray) : self::GET;
        }

        return new self($uri , $callback, $method);
        
    }

    /**
     * @return string
     */
    public function getMethod():string
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

    public static function create(string|array $route):RouteHandler
    {
        return is_array($route) ? self::fromArray($route) : self::fromString($route);
    }




}