<?php 

namespace Core\Classes\Route;

use Closure;
use Core\Traits\Utils;
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
        return Utils::callClosure($functionToCall, $args);
    }

}