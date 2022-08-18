<?php


if (!defined('ROOTDIR')) {
    define('ROOTDIR', __DIR__.'\\..');
}

if (file_exists(ROOTDIR.DIRECTORY_SEPARATOR.'.env')) {
    if ($env = parse_ini_file(ROOTDIR.DIRECTORY_SEPARATOR.'.env')) {
        foreach ($env as $key => $value) {
            putenv("$key=$value");
        }
    }
} else {
    die("no environment file detected (.env)");
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

if (!function_exists('dd')) {
    /**
     * Dump and die.
     * @param mixed ...$args
     *
     * @return void
     */
    function dd(...$args)
    {
        ob_clean();
        $title = 'VAR DUMP';
        if(php_sapi_name() === 'cli'){
            echo "\e[1;31;47m$title\e[0m\n";            
            array_map(function($arg) { echo "\n".var_export($arg)."\n"; }, func_get_args());
        }else{
            echo "<h3>$title</h3><pre>";
            array_map(function($arg) { highlight_string("\n" . var_export($arg) . "\n"); }, func_get_args());
            
        }
        die;
    }
}
