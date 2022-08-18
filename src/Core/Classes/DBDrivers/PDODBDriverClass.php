<?php

namespace Core\Classes\DBDrivers;

use Core\Classes\StorageDrivers\PDODriver;
use Core\Interfaces\IDBDriver;
use PDO;
use PDOException;

class PDODBDriverClass implements IDBDriver
{


    /**
     * @param string $driver
     * @return PDODriver::class
     */
    public static function getDriverClass(string $driver): string
    {   
        
        self::checkPDODriverAvailability($driver);

        $driverClass = PDODriver::class;

        return $driverClass;
    }

    public static function checkPDODriverAvailability(string $driver,$throw = true): string|false
    {
        $driver = strtolower($driver);

        if(str_starts_with($driver,'pdo:')){
            $driver = explode(':',$driver)[1];
        }
        if(!in_array($driver,PDO::getAvailableDrivers())){
            if($throw)
                throw new PDOException("pdo $driver is not supported", 1);                
            return false;
        }
        return $driver;

    }
}
