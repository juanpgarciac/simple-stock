<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\StorageMapper;
use InvalidArgumentException;

class StorageDriverFactory
{
    /**
     * @param key-of<DBDriver::DRIVERS>|string $storageDriver
     * @param DBConfiguration|null $DBConfiguration
     *
     * @return StorageMapper
     */
    public static function createStorage(string $storageDriver, ?DBConfiguration $DBConfiguration = null): StorageMapper
    {
        $storageMapperClass = DBDriver::getDriverClass($storageDriver);
        if ($storageMapperClass === FakeDBDriver::class) {
            return new $storageMapperClass();
        }

        if (is_null($DBConfiguration)) {
            throw new InvalidArgumentException("DBConfiguration cannot be null for $storageMapperClass", 1);
        }


        return new $storageMapperClass($DBConfiguration);
    }
}
