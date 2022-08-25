<?php

namespace Core\Classes;

use Exception;

abstract class Model
{
    /**
     * Receives an  associative array, set the properties accordingly and return a new Model instance
     * @param array $args
     * 
     * @return Model
     */
    public static function fromArray(array $args = []):Model
    {
        $self = new static();
        foreach ($args as $key => $value) {
            if(property_exists($self,$key)){
                if(!is_null($value) || gettype($self->$key) === "NULL")
                    $self->$key = $value;
            }
        }
        return $self;
    }

    /**
     * (alias for fromArray) Receives an  associative array, set the properties accordingly and return a new Model instance
     * @param array $args
     * 
     * @return Model
     */
    public static function fromState(array $args = []):Model
    {
        return self::fromArray($args);
    }

    /**
     * creator function. Receives an array or nothing, set the properties accordingly and return a new Model instance
     * @param mixed $args
     * 
     * @return Model
     */
    public static function create(mixed $args = []):Model
    {
        if(is_array($args))
            return self::fromArray($args);
        return new static();
    }

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
    public function getValue(string $fieldName, bool $exceptionCheck = true): mixed
    {
        if (property_exists($this::class, $fieldName)) {
            return isset($this->$fieldName) ? $this->$fieldName : null;
        }
        if($exceptionCheck)
            throw new Exception("Field $fieldName doesn't exist in ".$this::class." scope", 1);
        return null;
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
