<?php

namespace Core\Classes;

class DBConfiguration
{
    private $db = null;
    private $host = null;
    private $port = null;
    private $username = null;
    private $password = null;
    private $socket = null;

    /**
     * @param string $db
     * @param string $username
     * @param string $password
     * @param string $host
     * @param string $port
     *
     * @return void
     */
    public function __construct($db, $username = 'root', $password = '', $host = 'localhost', $port = '3306', $socket = null)
    {
        $this->db = $db;
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->socket = $socket;
    }

    public function getDB()
    {
        return $this->db;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getHost()
    {
        return $this->host;
    }
    public function getPort()
    {
        return $this->port;
    }
    public function getSocket()
    {
        return $this->socket;
    }

    public static function FromEnvFile()
    {
        return new self(env('DB_NAME'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_HOST'), env('DB_PORT'));
    }
}
