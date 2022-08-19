<?php

namespace Core\Classes;

use Core\Classes\DBConfiguration;
use Core\Classes\StorageDrivers\StorageDriverFactory;
use Core\Interfaces\IStorageDriver;

class App extends Singleton
{
    private static ?App $instance = null;
    private Router $appRouter;
    private DBConfiguration $appDBConfiguration;
    private IStorageDriver $appStorage;


    final private function __construct()
    {
        $this->appDBConfiguration = DBConfiguration::FromEnvFile();
        $this->appStorage =  StorageDriverFactory::createStorage($this->appDBConfiguration);
        $this->appRouter = Router::getInstance();
    }

    public static function getInstance(): App
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function getAppStorage(): IStorageDriver
    {
        return $this->appStorage;
    }

    public function getDBConfiguration(): DBConfiguration
    {
        return $this->appDBConfiguration;
    }

    public function getRouter(): Router
    {
        return $this->appRouter;
    }
}
