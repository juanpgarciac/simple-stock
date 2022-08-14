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
    function env($key, $default = null)
    {
        return getenv($key) ?? $default;
    }
}

if (!function_exists('dd')) {
    function dd(...$args)
    {
        ob_clean();
        echo '<pre>';
        die(var_dump($args));
        echo '</pre>';
        
    }
}
