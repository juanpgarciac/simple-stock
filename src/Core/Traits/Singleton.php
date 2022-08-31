<?php

namespace Core\Traits;

use Core\Interfaces\ISingleton;

trait Singleton
{
    private static ?ISingleton $instance = null;

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __clone() { }
    public function __sleep() { }
    public function __wakeup() { }
}
