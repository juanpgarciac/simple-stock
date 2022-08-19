<?php
namespace Core\Classes;

use Core\Interfaces\ISingleton;

abstract class Singleton implements ISingleton
{
 
    public function __clone():void
    {

    }

    public function __wakeup(): void
    {
        throw '';
    }
}