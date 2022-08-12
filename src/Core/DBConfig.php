<?php

namespace Core;

class DBConfig
{
    public $db = null;
    public $host = null;
    public $port = null;
    public $username = null;
    public $password = null;

    /**
     * @param string $db
     * @param string $username
     * @param string $password
     * @param string $host
     * @param string $port
     * 
     * @return void
     */
    function __construct($db,$username = 'root', $password = '', $host = 'localhost', $port = '3306')
    {

        $this->db = $db;
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;

        
    }

    static public function FromEnvFile()
    {
        
        return new self(env('DB_NAME'),env('DB_USERNAME'),env('DB_PASSWORD'),env('DB_HOST'),env('DB_PORT'));
    }


}