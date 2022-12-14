<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\StorageDrivers\FakeDBDriver;
use Core\Interfaces\IDBDriver;
use InvalidArgumentException;

class FakeDBDriverClass implements IDBDriver
{
    public const FAKEDBDRIVER = 'fakedb';
    public const FAKEDB_DRIVER_CLASS = FakeDBDriver::class;


    /**
     * @param string $driver
     *
     * @return FakeDBDriverClass::FAKEDB_DRIVER_CLASS
     */
    public static function getDriverClass(string $driver): string
    {
        if ($driver !== self::FAKEDBDRIVER) {
            throw new InvalidArgumentException("driver parameter should be '".self::FAKEDBDRIVER."', '$driver' received ", 1);
        }
        return FakeDBDriver::class;
    }
}
