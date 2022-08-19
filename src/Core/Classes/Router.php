<?php

namespace Core\Classes;

final class Router extends Singleton
{
    private static ?Router $instance = null;

    public static function getInstance(): Router
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
