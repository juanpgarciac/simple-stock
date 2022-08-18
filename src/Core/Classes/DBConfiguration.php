<?php

namespace Core\Classes;

use Core\Classes\DBDrivers\FakeDBDriverClass;

class DBConfiguration
{
    private string $driver;
    private string $db;
    private string $host = '';
    private ?int $port = null;
    private string $username = '';
    private string $password = '';
    private string $socket = '';

    /**
     * @param string $driver
     * @param string $db
     * @param string $username
     * @param string $password
     * @param string $host
     * @param int|null $port
     * @param string $socket
     */
    public function __construct(string $driver = FakeDBDriverClass::FAKEDBDRIVER, string $db = 'fakedb', string $username = '', string $password = '', string $host = 'localhost', int|null $port = null, string $socket = '')
    {
        $this->driver = $driver;
        $this->db = $db;
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->socket = $socket;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }
    /**
     * @return string
     */
    public function getDB(): string
    {
        return $this->db;
    }
    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }
    /**
     * @return int|null
     */
    public function getPort(): int|null
    {
        return $this->port;
    }
    /**
     * @return string
     */
    public function getSocket(): string
    {
        return $this->socket;
    }

    /**
     * @return DBConfiguration
     */
    public static function FromEnvFile(): DBConfiguration
    {
        return new self(
            env_str('DB_DRIVER'),
            env_str('DB_NAME'),
            env_str('DB_USERNAME'),
            env_str('DB_PASSWORD'),
            env_str('DB_HOST'),
            (int) env_str('DB_PORT'),
            env_str('DB_SOCKET')
        );
    }
}
