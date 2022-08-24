<?php

namespace Core\Classes\Route;

final class Request
{
    /**
     * @var array<mixed>
     */
    private array $parameters = [];

    private string $method = Request::GET;

    public const GET = 'GET';
    public const POST = 'POST';

    public const METHODS = [
        Request::GET,
        Request::POST
    ];

    public function __construct($method = Request::GET, $requestData = null, $uriParameters = false )
    {
        
        if ($uriParameters === false) {
            $uriParameters = [];
        }
        if (empty($requestData)) {
            $requestData = [];
        }else{
            $requestData = is_array($requestData) ? $requestData : [$requestData];
            $requestData = array_merge($requestData,['_request' => $requestData], ['_'.$method => $requestData]);
        }

        $this->parameters = array_merge($requestData, $uriParameters);
        $this->method = $method;
        

    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParameter(string $name)
    {
        $key = $name;
        $parameters = $this->getParameters();
        $method = $this->getMethod();

        //dd($name, $parameters);

        if(isset($parameters[$name])){
            return $parameters[$name];
        }
        
        if(isset($parameters[$method]) && isset($parameters[$method][$name])){
            $parameters[$method][$name];
        }

        if(isset($parameters['_request']) && isset($parameters['_request'][$name])){
            return $parameters['_request'][$name];
        }
        return null;
    }
}