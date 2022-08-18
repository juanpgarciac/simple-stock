<?php

namespace Core\Classes\StorageDrivers;

use Core\Classes\DBConfiguration;
use Core\Classes\DBDrivers\FakeDBDriverClass;
use Core\Classes\DBDrivers\PDODBDriverClass;
use Core\Classes\DBDrivers\SQLDBDriverClass;
use Core\Interfaces\IStorageDriver;
use InvalidArgumentException;

class StorageDriverFactory
{
    /**
     * @param DBConfiguration|null $DBConfiguration
     *
     * @return IStorageMapper
     */
    public static function createStorage(?DBConfiguration $DBConfiguration = null): IStorageDriver
    {
        $storageDriver = $DBConfiguration?->getDriver() ?? FakeDBDriverClass::FAKEDBDRIVER;

        if($storageDriver ===  FakeDBDriverClass::FAKEDBDRIVER){
            $storageMapperClass = FakeDBDriverClass::getDriverClass($storageDriver);
        }else if(str_starts_with($storageDriver, 'pdo:')){
            $storageMapperClass = PDODBDriverClass::getDriverClass($storageDriver);
        }else{
            $storageMapperClass = SQLDBDriverClass::getDriverClass($storageDriver);
            if (is_null($DBConfiguration)) {
                throw new InvalidArgumentException("DBConfiguration cannot be null for $storageMapperClass", 1);
            }
        }

        return new $storageMapperClass($DBConfiguration);
    }
}
