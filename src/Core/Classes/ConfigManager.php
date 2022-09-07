<?php

namespace Core\Classes;

use Core\Interfaces\ISingleton;
use Core\Traits\Singleton;

final class ConfigManager implements ISingleton
{
    use Singleton;

    private array $configurations = [];

    private function __construct()
    {
        $this->configurations = envPool();
    }

    public static function configurations(): array
    {
        return self::getInstance()->configurations;
    }

    public static function configuration(string $name): string
    {
        $name = strtoupper($name);
        return array_key_exists($name, self::configurations()) ? self::configurations()[$name] : '';
    }

    public static function dir(string $dir): string
    {
        return self::getInstance()->configuration($dir."_DIR");
    }

    public static function set(string $key, string $value): void
    {
        $key = strtoupper($key);
        if (array_key_exists($key, self::configurations())) {
            self::configurations()[$key] = $value;
            trigger_error("Changing $key should be for testing purposes only.", E_USER_WARNING);
        }
    }
}
