<?php 

namespace Core;

use Core\SQLBase;

class MySQL extends SQLBase
{
    private DBConfig $DBConfig;
    private $link = null;

    public function __construct(DBConfig $DBConfig)
    {
        $this->DBConfig = $DBConfig;
    }

    public function connect()
    {
        if(!$this->link){
            $this->link = mysqli_connect($this->DBConfig->host,$this->DBConfig->username,$this->DBConfig->password,$this->DBConfig->db,$this->DBConfig->port);
        }
    }

    public  function close()
    {
        if($this->link){
            mysqli_close($this->link);
            $this->link = null;
        }
    }


    public function results($fields,$conditions,$table)
    {
        $results = [];
        $this->connect();
        $resource = mysqli_query($this->link, self::query($fields,$conditions,$table));
        foreach (mysqli_fetch_assoc($resource) as $row) {
            $results[] = $row;
        }
        $this->close();
        return $results;
    }

    public function insert($fields, $values, $table)
    {
        $this->connect();
        $query = self::insertQuery($fields, $values, $table);
        mysqli_query($this->link, $query);
        $this->close();
    }

}