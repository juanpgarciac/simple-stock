<?php 

namespace Core\Classes\DBDrivers;

use Core\Classes\DBConfiguration;
use Core\Interfaces\IDB;
use Core\Traits\SQLUtils;

class MySQL implements IDB
{
    use SQLUtils;

    private DBConfiguration $DBConfig;
    private $link = null;
    

    public function __construct(DBConfiguration $DBConfig)
    {
        $this->DBConfig = $DBConfig;
    }

    public function connect()
    {
        if(!$this->link || !is_a($this->link,'mysqli') ){
            $this->link = mysqli_connect(
                $this->DBConfig->getHost(),
                $this->DBConfig->getUsername(),
                $this->DBConfig->getPassword(),
                $this->DBConfig->getDB(),
                $this->DBConfig->getPort(),
                $this->DBConfig->getSocket()
            );
        }
    }

    public  function close()
    {
        if($this->link && is_a($this->link,'mysqli')){
            mysqli_close($this->link);
        }
        $this->link = null;
    }


    public function results($fields,$conditions,$table)
    {
        $records = [];
        $this->connect();
        $query = SQLUtils::selectQuery($fields,$conditions,$table);
        $result = mysqli_query($this->link, $query);
        while ($row =  mysqli_fetch_assoc($result)){
            $records[] = $row;
        }
        mysqli_free_result($result);
        $this->close();
        return $records;
    }

    public function resultByID($recordID,$table, $id_field = 'id')
    {        
        $results = $this->results(['*'],["$id_field = $recordID"],$table);
        return count($results)>0 ? $results[0] : null;
    }

    public function insertRecord($recordData, $table, $id_field = 'id'): int
    {
        $id = null;
        $this->connect();
        $query = SQLUtils::insertQuery($recordData,$table);
        if(mysqli_query($this->link, $query)){
            $id = $this->link->insert_id;
        }
        $this->close();
        return $id;
    }

    public function updateRecord($recordID, $recordData, $table, $id_field = 'id')
    {
        $this->connect();
        $query = SQLUtils::updateQuery($recordData,["id = $recordID"],$table);
        mysqli_query($this->link, $query);
        $this->close();
    }

    public function deleteRecord($recordID,$table, $id_field = 'id')
    {
        $this->connect();
        $recordIDs = is_array($recordID) ? implode(", ", $recordID) : $recordID;
        $query = SQLUtils::deleteQuery(["$id_field in ( $recordIDs )"], $table);
        mysqli_query($this->link, $query);
        $this->close();
    }

    public function deleteManyRecords($conditions,$table)
    {
        $this->connect();
        $query = SQLUtils::deleteQuery($conditions, $table);
        mysqli_query($this->link, $query);
        $this->close();
    }


}