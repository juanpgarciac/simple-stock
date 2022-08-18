<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IStorageMapper;
use InvalidArgumentException;

class StorageDriverFactory
{
    /**
     * @param DBConfiguration|null $DBConfiguration
     *
     * @return StorageMapper
     */
    public static function createStorage(?DBConfiguration $DBConfiguration = null): IStorageMapper
    {
        $storageDriver = $DBConfiguration?->getDriver() ?? DBDriver::FAKEDBDRIVER;

        if(str_starts_with($storageDriver, 'pdo:')){
            $storageMapperClass = PDODBDriver::getDriverClass($storageDriver);
        }else{
            $storageMapperClass = DBDriver::getDriverClass($storageDriver);
        }    
        
        if ($storageMapperClass === FakeDBDriver::class) {
            return new $storageMapperClass();
        }

        if (is_null($DBConfiguration)) {
            throw new InvalidArgumentException("DBConfiguration cannot be null for $storageMapperClass", 1);
        }


        return new $storageMapperClass($DBConfiguration);
    }
}
