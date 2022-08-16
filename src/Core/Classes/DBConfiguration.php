<?php

namespace Core\Classes;

class DBConfiguration
{
    private string $db;
    private string $host = '';
    private ?int $port = null;
    private string $username = '';
    private string $password = '';
    private string $socket = '';

    /**
     * @param string $db
     * @param string $username
     * @param string $password
     * @param string $host
     * @param int|null $port
     * @param string $socket
     */
    public function __construct(string $db, string $username = 'root', string $password = '', string $host = 'localhost', int|null $port = null, string $socket = '')
    {
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
        return new self(env('DB_NAME'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_HOST'), env('DB_PORT'), env('DB_SOCKET'));
    }
}
