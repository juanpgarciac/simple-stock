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
     * Get environment value
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    function env(string $key, string $default = ''): string
    {
        $env = getenv($key, true);
        return !empty($env) ? $env : $default;
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

function slash():string
{
    return defined('DIRECTORY_SEPARATOR')?DIRECTORY_SEPARATOR:'/';
}

function path(string $dir,string $file):string
{
    return $dir.slash().$file;
}
