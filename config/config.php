<?php 

if(!defined('ROOTDIR'))
    define('ROOTDIR',__DIR__.'\\..');
    
if (file_exists(ROOTDIR.DIRECTORY_SEPARATOR.'.env')) {
    $env = parse_ini_file(ROOTDIR.DIRECTORY_SEPARATOR.'.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
} else {
    die("no environment file detected (.env)");
}

if (!function_exists('env')) {
    /**
     * Get environment value
     * @param mixed $key
     * @param mixed $default
     * 
     * @return [type]
     */
    function env($key, $default = null)
    {
        return getenv($key) ?? $default;
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
        echo '<pre>';
        foreach ($args as $arg) {
            var_dump($arg);
        }
        
        echo '</pre>';
        die;
        
    }
}