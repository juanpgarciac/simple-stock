<?php
namespace Core\Classes;

use ReflectionFunction;
use ReflectionMethod;

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
    public function __construct(string $uri, string|array|callable  $callback = '', string $method = RouteHandler::GET)    
    {
        if(empty($uri))
            throw new \InvalidArgumentException("The route cannot be an empty string", 1);

        $this->uri = $uri;

        $this->callback = self::createCallback($callback);

        if(!in_array($method,self::METHODS))
            throw new \InvalidArgumentException("Method parameter should be ".implode('|',self::METHODS).", '$method' given", 1);
        $this->method = $method;

        $this->id = self::getURIPattern($this->uri)['path'];

        $this->parameters = self::getURIPattern($this->uri)['parameters'];
        
    }

    private static function createCallback(string|array|callable $callback):array|callable
    {
        if(is_callable($callback) || is_array($callback)){
            return $callback; 
        }

        if(preg_match("#(\w+(\:\:|\@)\w+)#",preg_quote($callback)))
        {   
            return preg_split("#(\:\:|\@)#",$callback);
        }

        return function($output = null) use ($callback) { return $output ?? $callback ; };
    }

    public function getBaseURI()
    {
        return $this->uri;
    }

    public function getCallback():array|callable
    {
        return $this->callback;
    }

    public function callback(mixed $args = [])
    {
        $functionToCall = $this->getCallback();
        if(is_array($functionToCall)){ 
            if(count($functionToCall) !== 2 )
                throw new \InvalidArgumentException("callback array should be defined as [class name, methodname]", 1);

            $class = isset($functionToCall['class'])?$functionToCall['class']:$functionToCall[0];
            $method = isset($functionToCall['method'])?$functionToCall['method']:$functionToCall[1];
            if(!class_exists($class)){
                $classes = preg_grep("#".preg_quote($class)."#",  get_declared_classes() );
                if(empty($classes)){
                    throw new \InvalidArgumentException("$class is not defined", 1);                    
                }
                $class = $classes[array_key_first($classes)];
            }
            //$classReflection = new ReflectionClass($class);                    
            $callbackReflection = new ReflectionMethod($class,$method);
            if(!$callbackReflection->isStatic()){
                $functionToCall = (new $class())->$method[1];
            }
            $functionToCall = [$class, $method];
        }else{
            $callbackReflection = new ReflectionFunction($functionToCall);
        }
        
        $callbackParameters = $callbackReflection->getParameters();
        /* */

        $data = [];
        $parametersCount = $callbackReflection->getNumberOfParameters();
        if($parametersCount > 0){
            //var_dump($callbackParameters, $args); die;
            $args = is_array($args) ? $args : [ $callbackParameters[0]->name => $args ];
            $emptyParameters = 0;
            foreach($callbackParameters as $arg){
                if(isset($args[$arg->name])){
                    $data[$arg->name] = $args[$arg->name];
                    unset($args[$arg->name]);
                }else{
                    $data[$arg->name] = null;
                    $emptyParameters++;
                }
                
            }
            if(count($args) > 0  &&  $emptyParameters > 0){
                foreach($data as $key => $arg){
                    if(count($args) == 0) break;
                    if(is_null($arg)){
                        $data[$key] = array_shift($args);
                    }
                }                
            }
        }
        return call_user_func_array($functionToCall, $data );
       
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
        return new self('/404', 'Not Found');
    }



}