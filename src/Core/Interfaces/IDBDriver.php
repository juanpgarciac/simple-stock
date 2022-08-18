<?php

namespace Core\Interfaces;

interface IDBDriver
{
    public static function getDriverClass(string $driver): string;
}
