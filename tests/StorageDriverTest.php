<?php

declare(strict_types=1);

use Core\Classes\DBConfiguration;
use Core\Classes\StorageDrivers\PDODriver;
use Core\Classes\StorageDrivers\FakeDBDriver;
use Core\Classes\StorageDrivers\StorageDriverFactory;
use PHPUnit\Framework\TestCase;

final class StorageDriverTest extends TestCase
{
    public function test_storage_only_run_supported_drivers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        StorageDriverFactory::createStorage(new DBConfiguration('NotASupportedDriver'));
    }

    public function test_storage_creation(): void
    {
        $storage = StorageDriverFactory::createStorage();

        $this->assertInstanceOf(FakeDBDriver::class, $storage);

        $storage = StorageDriverFactory::createStorage(new DBConfiguration());

        $this->assertInstanceOf(FakeDBDriver::class, $storage);
    }

    public function test_pdo_unsupported_driver(): void
    {
        $this->expectException(PDOException::class);
        StorageDriverFactory::createStorage(new DBConfiguration('pdo:unsuported'));
    }

    public function test_pdo_supported_driver(): void
    {

        $pdodrivers = PDO::getAvailableDrivers();
        if(!empty($pdodrivers)){
            
            $dbconfig = new DBConfiguration('pdo:'.$pdodrivers[0]);
            $storage = StorageDriverFactory::createStorage($dbconfig);
            $this->assertInstanceOf(PDODriver::class,$storage);

        }else{
            $this->markTestSkipped('No PDO driver supported to test on this server');
        }


    }
}
