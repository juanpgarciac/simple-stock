<?php

namespace Core\Classes\DBDrivers;

use Core\Interfaces\IDBDriver;
use InvalidArgumentException;

class DBDriver implements IDBDriver
{
    public const FAKEDBDRIVER = 'fakedb';
    public const MYSQLDRIVER = 'mysql';
    public const POSTGRESDRIVER = 'postgres';
    public const SQLITE3DRIVER = 'sqlite3';

    private const DRIVERS = [
        self::FAKEDBDRIVER => FakeDBDriver::class,
        self::MYSQLDRIVER => MySQLDriver::class,
        self::POSTGRESDRIVER => PostgreSQLDriver::class,
        self::SQLITE3DRIVER => SQLite3Driver::class,
    ];


    /**
     * @param key-of<DBDriver::DRIVERS>|string $driver
     * @return value-of<DBDriver::DRIVERS>
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
