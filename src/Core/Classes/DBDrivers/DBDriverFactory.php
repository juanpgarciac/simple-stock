<?php
namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IDBDriver;
use InvalidArgumentException;

class DBDriverFactory
{
    public const FAKEDBDRIVER = 'fakedb';
    public const MYSQLDRIVER = 'mysql';    
    public const POSTGRESDRIVER = 'postgres';
    public const SQLITE3DRIVER = 'sqlite3';


    private static $drivers = [
        self::FAKEDBDRIVER => FakeDBDriver::class,
        self::MYSQLDRIVER => MySQLDriver::class,        
        self::POSTGRESDRIVER => PostgreSQLDriver::class,
        self::SQLITE3DRIVER => SQLite3Driver::class,
    ];


    /**
     * @param FAKEDBDRIVER|MYSQLDRIVER|POSTGRESDRIVER|SQLITE3DRIVER $driver
     * @param DBConfiguration|null $DBConfiguration
     * 
     * @return IDBDriver
     */
    public static function createDBDriver($driver = DBDriverFactory::FAKEDBDRIVER,?DBConfiguration $DBConfiguration = null): IDBDriver
    {
        if(!isset(self::$drivers[$driver]))
            throw new InvalidArgumentException("$driver driver is not supported", 1);
                        
        return new self::$drivers[$driver]($DBConfiguration);
    }
}
