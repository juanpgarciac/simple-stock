<?php

namespace Core\Classes;

use Exception;

abstract class Model
{
    /**
     * @param string $id_field
     *
     * @return string
     */
    public function id(string $id_field = 'id'): string
    {
        return $this->getValue($id_field)."";
    }

    /**
     * @param string $fieldName
     *
     * @return string|int|float|null
     */
    public function getValue(string $fieldName): mixed
    {
        if (property_exists($this::class, $fieldName)) {
            return $this->$fieldName;
        }
        throw new Exception("Field $fieldName doesn't exist in ".$this::class." scope", 1);
    }

    /**
     * @param string $fieldName
     * @param mixed $value
     * @param bool $exceptionCheck
     *
     * @return void
     */
    public function setValue(string $fieldName, mixed $value, bool $exceptionCheck = true): void
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
