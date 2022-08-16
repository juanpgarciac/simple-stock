<?php

namespace Core\Traits;

use InvalidArgumentException;

class Utils
{
    /**
     * @param string $exp1
     * @param string $operator
     * @param string $exp2
     *
     * @return bool
     */
    public static function operate(string $exp1, string $operator, string $exp2): bool
    {
        switch ($operator) {
            case '=':
                return $exp1 === $exp2;
            case '!=':
            case '<>':
                return $exp1 != $exp2;
            case '>':
                return $exp1 > $exp2;
            case '>=':
                return $exp1 > $exp2;
            case '<':
                return $exp1 < $exp2;
            case '<=':
                return $exp1 <= $exp2;
            case 'like':
                return $exp1 == $exp2;

        }
        throw new InvalidArgumentException("No valid operator given", 1);
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    public static function baseClassName(string $classname): string
    {
        return (substr($classname, strrpos($classname, '\\') + 1));
    }
}
