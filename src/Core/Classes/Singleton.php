<?php

namespace Core\Classes;

use Core\Interfaces\ISingleton;

abstract class Singleton implements ISingleton
{
    public function __clone()
    {
    }

    public function __wakeup(): void
    {
        throw new \Exception("Cannot unserialize this class");
    }
}
