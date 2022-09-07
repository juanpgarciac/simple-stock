<?php

namespace Core\Classes;

use Core\Classes\DBConfiguration;
use Core\Classes\Route\Router;
use Core\Classes\StorageDrivers\StorageDriverFactory;
use Core\Interfaces\ISingleton;
use Core\Interfaces\IStorageDriver;
use Core\Traits\Singleton;

class App implements ISingleton
{
    use Singleton;

    private Router $appRouter;
    private DBConfiguration $appDBConfiguration;
    private IStorageDriver $appStorage;
    private ConfigManager $configManager;


    final private function __construct()
    {
        $this->appDBConfiguration = DBConfiguration::FromEnvFile();
        $this->appStorage =  StorageDriverFactory::createStorage($this->appDBConfiguration);
        $this->appRouter = Router::getInstance();
        $this->configManager = ConfigManager::getInstance();
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

    public function getConfigManager(): ConfigManager
    {
        return $this->configManager;
    }
}
