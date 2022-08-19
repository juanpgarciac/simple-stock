<?php
namespace Core\Classes;

use Core\Classes\DBConfiguration;
use Core\Classes\StorageDrivers\StorageDriverFactory;

class App extends Singleton
{
    private $appDBConfiguration = null;
    private $appStorage = null;
    private $appRouter = null;
    private static $instance = null;
    

    private function __construct()
    {
        $this->appDBConfiguration = DBConfiguration::FromEnvFile();
        $this->appStorage =  StorageDriverFactory::createStorage($this->appDBConfiguration);
        $this->appRouter = Router::getInstance();
    }    

    public static function getInstance():App
    {
        if(self::$instance == null)
            self::$instance = new static();
        return self::$instance;
    }

    public function getAppStorage()
    {
        return $this->appStorage;
    }

    public function getDBConfiguration()
    {
        return $this->appDBConfiguration;
    }

    public function getRouter(){
        return $this->appRouter;
    }

}