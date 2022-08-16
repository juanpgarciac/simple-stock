<?php 

if(!defined('ROOTDIR'))
    define('ROOTDIR',__DIR__.'\\..');
    
if (file_exists(ROOTDIR.DIRECTORY_SEPARATOR.'.env')) {
    if($env = parse_ini_file(ROOTDIR.DIRECTORY_SEPARATOR.'.env')){
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
     * @param mixed $default
     * 
     * @return mixed
     */
    function env(string $key, $default = null): mixed
    {
        return getenv($key, true) ?? $default;
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