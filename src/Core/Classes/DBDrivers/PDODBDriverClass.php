<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\StorageDrivers\PDODriver;
use Core\Interfaces\IDBDriver;
use PDO;
use PDOException;

class PDODBDriverClass implements IDBDriver
{

    public const PDOMYSQLDRIVER = 'mysql';
    public const PDOPOSTGRESDRIVER = 'pgsql';
    public const PDOSQLITE3DRIVER = 'sqlite';

    private const DRIVERS = [
        self::PDOMYSQLDRIVER => PDODriver::class,
        self::PDOPOSTGRESDRIVER => PDODriver::class,
        self::PDOSQLITE3DRIVER => PDODriver::class
    ];

    /**
     * @param string $driver
     * @return PDODriver::class
     */
    public static function getDriverClass(string $driver): string
    {   
        
        $driverClass = self::DRIVERS[self::checkPDODriverAvailability($driver)];

        return $driverClass;
    }

    public static function checkPDODriverAvailability(string $driver,$throw = true): string|false
    {
        $driver = strtolower($driver);

        if(str_starts_with($driver,'pdo:')){
            $driver = explode(':',$driver)[1];
        }
        if(!in_array($driver,PDO::getAvailableDrivers()) || !array_key_exists($driver, self::DRIVERS)){
            if($throw)
                throw new PDOException("PDO $driver is not supported", 1);                
            return false;
        }
        return $driver;

    }
}
