
<?php

use Core\Classes\DBConfiguration;
use Core\Classes\StorageDrivers\StorageDriverFactory;

class App
{
    private $appStorage = null;
    private static $instance = null;

    private function __construct()
    {
        
        $this->appStorage =  StorageDriverFactory::createStorage(DBConfiguration::FromEnvFile());
    }

    public static function getInstance()
    {
        if(self::$instance == null)
            self::$instance = new static();
        return self::$instance;
    }

    public function getAppStorage()
    {
        return $this->appStorage;
    }

    public function __clone()
    {
        
    }
    public function __wakeup()
    {
        throw '';
    }


}
