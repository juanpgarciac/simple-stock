<?php 

namespace Core\Traits;

use InvalidArgumentException;

class Utils
{
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
}

