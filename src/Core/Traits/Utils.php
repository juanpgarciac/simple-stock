<?php 

namespace Core\Traits;

class Utils
{
    public static function operate($exp1, $operator, $exp2){
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
    }
}

