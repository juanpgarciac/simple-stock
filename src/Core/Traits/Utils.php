<?php

namespace Core\Traits;

use Core\Interfaces\ISingleton;
use InvalidArgumentException;

class Utils
{
    /**
     * @param string $classname
     *
     * @return string
     */
    public static function baseClassName(string $classname): string
    {
        return (substr($classname, (strrpos($classname, '\\') ?: 0) + 1));
    }


    /**
     * @param mixed $functionToCall
     * @param mixed $args
     * @param string $classesDir
     * 
     * @return mixed
     */
    public static function callClosure(mixed $functionToCall, mixed $args = [], string $classesDir = SRC_DIR): mixed
    {
        if (is_array($functionToCall)) {
            if (count($functionToCall) !== 2) {
                throw new \InvalidArgumentException("callback array should be defined as [class name, methodname]", 1);
            }

            $class = isset($functionToCall['class']) ? $functionToCall['class'] : $functionToCall[0];
            $method = isset($functionToCall['method']) ? $functionToCall['method'] : $functionToCall[1];
            if (!class_exists($class)) {
                $classes = preg_grep("#".preg_quote($class)."#", get_declared_classes());
                if (empty($classes)) {
                    $classes = preg_grep("#".preg_quote($class)."#", array_keys(Utils::getClassesSourceFiles($classesDir)));
                    if (empty($classes)) {
                        throw new \InvalidArgumentException("$class is not defined", 1);
                    }
                }
                $class = $classes[array_key_first($classes)];
            }
            $classReflection = new \ReflectionClass($class);
            $callbackReflection = new \ReflectionMethod($class, $method);

            if (!$callbackReflection->isStatic()) {
                if($classReflection->implementsInterface(ISingleton::class)){
                    $functionToCall = $callbackReflection->getClosure($class::getInstance());
                }else{
                    $functionToCall = $callbackReflection->getClosure(new $class());
                }                
            } else {
                $functionToCall = [$class, $method];
            }
        } else {
            $callbackReflection = new \ReflectionFunction(\Closure::fromCallable($functionToCall));
        }

        $callbackParameters = $callbackReflection->getParameters();
        /* */

        $data = [];
        $parametersCount = $callbackReflection->getNumberOfParameters();
        if ($parametersCount > 0) {
            //var_dump($callbackParameters, $args); die;
            $args = is_array($args) ? $args : [ $callbackParameters[0]->name => $args ];
            $emptyParameters = 0;
            foreach ($callbackParameters as $arg) {
                if (isset($args[$arg->name])) {
                    $data[$arg->name] = $args[$arg->name];
                    unset($args[$arg->name]);
                } else {
                    $data[$arg->name] = null;
                    $emptyParameters++;
                }
            }
            if (count($args) > 0  &&  $emptyParameters > 0) {
                foreach ($data as $key => $arg) {
                    if (count($args) == 0) {
                        break;
                    }
                    if (is_null($arg)) {
                        $data[$key] = array_shift($args);
                    }
                }
            
            }
            return call_user_func_array($functionToCall, $data);
        }else{
            return call_user_func($functionToCall, $args);
        }
        return null;
    }

    /**
     * @param string $dir
     *
     * @return array
     */
    public static function getClassesSourceFiles(string $dir): array
    {
        $classes = [];
        $dir_iterator = new \RecursiveDirectoryIterator($dir);
        $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() == 'php') {
                $class =  str_replace(['.php',$dir,slash()], ['', '', '\\'], $file->getPathname());
                $classes[$class] = $file->getPathname();
            }
        }
        return $classes;
    }
}
