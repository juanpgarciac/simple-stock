<?php
namespace Core\Classes;

use ReflectionFunction;

final class RouteHandler
{
    public const GET = 'GET';
    public const POST = 'POST';
    

    public const PARAMETER_PATTERN = '[a-zA-Z0-9\_\-\.]+';

    public const METHODS = [
        RouteHandler::GET,
        RouteHandler::POST
    ];

    private string $id;
    private string $uri;
    private string $method =  '';
    private mixed $callback;    
    private array $parameters = [];

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

        $this->callback = is_callable($callback) ? $callback : fn($callback) => $callback;

        if(!in_array($method,self::METHODS))
            throw new \InvalidArgumentException("Method parameter should be ".implode('|',self::METHODS).", '$method' given", 1);
        $this->method = $method;

        $this->id = self::getURIPattern($this->uri)['path'];

        $this->parameters = self::getURIPattern($this->uri)['parameters'];
        
    }

    public function getBaseURI()
    {
        return $this->uri;
    }

    public function getCallback():callable
    {
        return $this->callback;
    }

    public function callback(mixed $args = [])
    {
        if(is_callable($this->getCallback())){
            /* */
            $callbackReflection = new ReflectionFunction($this->getCallback());
            $callbackParameters = $callbackReflection->getParameters();
            /* */

            $data = [];
            if($callbackReflection->getNumberOfParameters() > 0){
                //var_dump($callbackParameters, $args); die;
                $args = is_array($args) ? $args : [ $callbackParameters[0]->name => $args ];
                foreach($callbackParameters as $arg){
                    $data[$arg->name] = isset($args[$arg->name]) ? $args[$arg->name] :  null  ;
                }
            }

            return call_user_func_array($this->getCallback(), $data );
        }
        return $this->getCallback();
    }

    public function id():string
    {
        return $this->id;
    }

    public function getURIParameters():array
    {
        return $this->parameters;
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
        return ['path' => empty($path)?preg_quote('/','/'):$path, 'parameters' => $parameters];
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

    public function getParametersValues(string $uri):array|false
    {
        if(empty($this->parameters))
            return false;

        $uriParts = explode('/', $uri);
        $values = [];

        foreach ($uriParts as $index => $part) {
            if(empty($part) || !array_key_exists($index, $this->parameters))
                continue;            
            $values[  $this->parameters[$index] ] = $part; 
        }

        return $values;
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