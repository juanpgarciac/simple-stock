<?php

declare(strict_types=1);

use Core\Classes\DBDrivers\DBDriver;
use Core\Classes\DBDrivers\FakeDBDriver;
use Core\Classes\DBDrivers\StorageDriverFactory;
use PHPUnit\Framework\TestCase;

final class StorageDriverTest extends TestCase
{
    public function test_storage_only_run_supported_drivers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        StorageDriverFactory::createStorage('NotASupportedDriver');
    }

    public function test_storage_creation(): void
    {
        $storage = StorageDriverFactory::createStorage(DBDriver::FAKEDBDRIVER);

        $this->assertInstanceOf(FakeDBDriver::class, $storage);
    }
}
