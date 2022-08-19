<?php
namespace Core\Classes;


final class Router extends Singleton
{
    private static $instance = null;

    public static function getInstance():Router
    {
        if(self::$instance == null)
            self::$instance = new static();
        return self::$instance;
    }
    
}