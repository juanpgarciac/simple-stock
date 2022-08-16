<?php

namespace Core\Classes;

use Exception;

abstract class Model
{
    public function id($id_field = 'id')
    {
        return $this->getValue($id_field);
    }

    public function getValue($fieldName)
    {
        if (property_exists($this::class, $fieldName)) {
            return $this->$fieldName;
        }
        throw new Exception("Field $fieldName doesn't exist in ".$this::class." scope", 1);
    }

    public function setValue($fieldName, $value, $exceptionCheck = true)
    {
        if (property_exists($this::class, $fieldName)) {
            $this->$fieldName = $value;
            return;
        }

        if ($exceptionCheck) {
            throw new Exception("Field $fieldName doesn't exist in ".$this::class." scope", 1);
        }
    }
}
