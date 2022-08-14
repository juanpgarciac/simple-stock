<?php 

namespace Core\Classes;

use Exception;

abstract class Model
{


    public function id()
    {
        if(isset($this->id))        
            return $this->id;
    }

    public function getValue($fieldName)
    {
        if(property_exists($this::class,$fieldName))
            return $this->$fieldName;
        throw new Exception("Field $fieldName doesn't exist in ".$this::class." scope", 1);
        
    }
}