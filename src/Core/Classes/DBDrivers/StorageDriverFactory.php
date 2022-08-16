<?php

declare(strict_types=1);

namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\StorageMapper;

class StorageDriverFactory
{
    /**
     * @param key-of<DBDriver::DRIVERS> $driver
     * @param DBConfiguration|null $DBConfiguration
     *
     * @return StorageMapper
     */
    public static function createStorage(string $driver, ?DBConfiguration $DBConfiguration = null): StorageMapper
    {
        $storageMapperClass = DBDriver::getDriverClass($driver);
        if ($storageMapperClass === FakeDBDriver::class) {
            return new $storageMapperClass();
        }

        return new $storageMapperClass($DBConfiguration);
    }
}
