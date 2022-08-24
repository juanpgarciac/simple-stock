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
        $id = $this->getValue($id_field);
        return !empty($id) ? $id."" : "";
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

    public function toArray(): array
    {
        return get_object_vars($this); 
    }

    public function toObject(): object
    {
        return (object)$this->toArray();
    }
}
