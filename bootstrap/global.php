<?php

/* Global functions */


if (!function_exists('dd')) {
    /**
     * Dump and die the arguments sent.
     *
     * @return void
     */
    function dd()
    {
        //ob_clean();
        $title = 'VAR DUMP';
        if (php_sapi_name() === 'cli') {
            echo "\e[1;31;47m$title\e[0m\n";
            array_map(function (mixed $arg) {
                echo "\n".var_export($arg, true)."\n";
            }, func_get_args());
        } else {
            echo "<h3>$title</h3><pre>";
            array_map(function (mixed $arg) {
                highlight_string("\n<?php\n" . var_export($arg, true) . "\n?>");
            }, func_get_args());
        }
        die;
    }
}

if (!function_exists('env')) {
    /**
     * Get environment value. If $set value is sent, it will replace existing value or add it if doesn't exist.
     *
     * @param string $key
     * @param string $set
     *
     * @return string
     * Return env variable requested/set.
     */
    function env(string $key, string $set = ''): string
    {
        if (!empty($set)) {
            $pool = envPool([$key => $set]);
        } else {
            $pool = envPool();
        }

        $env = array_key_exists($key, $pool) ? $pool[$key] : '';
        return $env;
    }
}

/**
 * @param string $filepath
 *
 * @return array<mixed>
 */
function arrayFromFile(string $filepath): array
{
    if (is_file($filepath) && file_exists($filepath)) {
        $arr = include $filepath;
        if (is_array($arr)) {
            return $arr;
        }
        throw new \InvalidArgumentException("includeArrFile: $filepath doesn't return a valid array");
    }
    throw new \InvalidArgumentException("includeArrFile: $filepath file cannot be found");
}

/**
 * @return string
 */
function slash(): string
{
    return defined('DIRECTORY_SEPARATOR') ? DIRECTORY_SEPARATOR : '/';
}

/**
 * @param string $dir
 * @param string $file
 *
 * @return string
 */
function path(string  ...$pathParts): string
{
    return implode(slash(), $pathParts);
}
