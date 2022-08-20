<?php
namespace Core\Classes;

final class RouteHandler
{
    public const GET = 'GET';
    public const POST = 'POST';
    

    public const PARAMETER_PATTERN = '[a-zA-Z0-9\_\-\.]+';

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

        $this->callback = $callback;

        if(!in_array($method,self::METHODS))
            throw new \InvalidArgumentException("Method parameter should be ".implode('|',self::METHODS).", '$method' given", 1);
        $this->method = $method;
        
    }

    public function getBaseURI()
    {
        return $this->uri;
    }

    public function getCallback():callable
    {
        return $this->callback;
    }

    public function callback($vars = [])
    {
        if(is_callable($this->getCallback())){
            return call_user_func_array($this->getCallback(), $vars);
        }
        return $this->getCallback();
    }

    public function id():string
    {
        return self::getURIPattern($this->uri)['path'];
    }

    public function getURIParameters():array
    {
        return self::getURIPattern($this->uri)['parameters'];
    }

    private static function getURIPattern(string $uri): array
    {
        $uriParts = explode('/', $uri);
        $parameters = [];
        $path = '';
        
        foreach ($uriParts as $index => $part) {
            if(empty($part))
                continue;
            if(str_starts_with($part,':')){
                $parameters[$index] = str_replace(':','',$part); 
                $path .= preg_quote('/','/'). self::PARAMETER_PATTERN;
            }else{
                $path .= preg_quote('/'.$part,'/');
            }
        }
        return ['path' => empty($path)?'\/':$path, 'parameters' => $parameters];
    }

    private static function fromArray(array $routeArray): RouteHandler
    {

        if(empty($routeArray) || (!isset($routeArray['uri']) && !is_string($routeArray[0]))){
            throw new \InvalidArgumentException("The array should at least have a string value as a URI, in the 'uri' or 0 index", 1);            
        }

        if(isset($routeArray['uri'])){
            $uri = $routeArray['uri'];
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

    public static function create(string|array|RouteHandler $route):RouteHandler
    {
        if($route instanceof RouteHandler)
            return $route;
        
        if(is_array($route))
            return self::fromArray($route);

        return self::fromString($route);
    }

    public static function notFoundRoute():RouteHandler
    {
        return new self('/404', function(){ echo 'Not Found'; return 'Not Found'; } );
    }



}