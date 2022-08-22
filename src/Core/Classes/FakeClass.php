<?php
namespace Core\Classes;

class FakeClass
{

    public static function doSomethingStatically():string
    {
        return 'something statically';
    }

    public static function doSomethingWithInstance():string
    {
        return 'something with instance';
    }

    public static function doSomethingStaticallyWithVars($var1, $var2):string
    {
        return "something statically using $var1 & $var2";
    }

    public static function doSomethingInstanceWithVars($var1, $var2):string
    {
        return "something with instance using $var1 & $var2";
    }
}