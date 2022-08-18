<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\StorageDrivers\MySQLDriver;
use Core\Classes\StorageDrivers\PostgreSQLDriver;
use Core\Classes\StorageDrivers\SQLite3Driver;
use Core\Interfaces\IDBDriver;
use InvalidArgumentException;

class SQLDBDriverClass implements IDBDriver
{
    public const MYSQLDRIVER = 'mysql';
    public const POSTGRESDRIVER = 'postgres';
    public const SQLITE3DRIVER = 'sqlite3';

    private const DRIVERS = [
        self::MYSQLDRIVER => MySQLDriver::class,
        self::POSTGRESDRIVER => PostgreSQLDriver::class,
        self::SQLITE3DRIVER => SQLite3Driver::class,
    ];


    /**
     * @param key-of<SQLDBDriverClass::DRIVERS>|string $driver
     * @return value-of<SQLDBDriverClass::DRIVERS>
     */
    public static function getDriverClass(string $driver): string
    {
        if (!array_key_exists($driver, self::DRIVERS)) {
            throw new InvalidArgumentException("$driver driver is not supported", 1);
        }

        $driverClass = self::DRIVERS[$driver];

        return $driverClass;
    }
}
