<?php

namespace Core\Interfaces;

interface ISingleton
{
    public static function getInstance():ISingleton;

    public function __clone():void;
    
    public function __wakeup():void;
    
}