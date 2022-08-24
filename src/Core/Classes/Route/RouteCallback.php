<?php 

namespace Core\Classes\Route;

use Closure;
use ReflectionFunction;
use ReflectionMethod;

final class RouteCallback 
{
    private mixed $callback = null;

    public function __construct(string|array|callable  $descriptor = '')
    {
        $this->callback = self::callbackBuilder($descriptor);
    }

    /**
     * @param string|array<mixed>|callable $callback
     *
     * @return array<mixed>|callable
     */
    private static function callbackBuilder(string|array|callable $callback): array|callable
    {
        if (is_callable($callback) || is_array($callback)) {
            return $callback;
        }

        if (preg_match("#^[\\a-zA-Z0-9_-]*(:{2}|@)\w+$#", $callback)) {
            return preg_split("#(\:{2}|\@)#", $callback);
        }
        return function ($output = null) use ($callback) {
            return $output ?? $callback ;
        };
    }

    private function getCallback():array|callable
    {
        return $this->callback;
    }

    /**
     * @param mixed $args
     *
     * @return mixed
     */
    public function __invoke(mixed $args = []): mixed
    {
        $functionToCall = $this->getCallback();
        if (is_array($functionToCall)) {
            if (count($functionToCall) !== 2) {
                throw new \InvalidArgumentException("callback array should be defined as [class name, methodname]", 1);
            }

            $class = isset($functionToCall['class']) ? $functionToCall['class'] : $functionToCall[0];
            $method = isset($functionToCall['method']) ? $functionToCall['method'] : $functionToCall[1];
            if (!class_exists($class)) {
                $classes = preg_grep("#".preg_quote($class)."#", get_declared_classes());
                if (empty($classes)) {
                    $classes = preg_grep("#".preg_quote($class)."#", array_keys(getClassesSourceFiles()));
                    if (empty($classes)) {
                        throw new \InvalidArgumentException("$class is not defined", 1);
                    }
                }
                $class = $classes[array_key_first($classes)];
            }
            //$classReflection = new ReflectionClass($class);
            $callbackReflection = new ReflectionMethod($class, $method);

            if (!$callbackReflection->isStatic()) {
                $functionToCall = $callbackReflection->getClosure(new $class());
            } else {
                $functionToCall = [$class, $method];
            }
        } else {
            $callbackReflection = new ReflectionFunction(Closure::fromCallable($functionToCall));
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


}