<?php 

if(!defined('ROOTDIR'))
    define('ROOTDIR',__DIR__.'\\..');
    
if (file_exists(ROOTDIR.DIRECTORY_SEPARATOR.'env.php')) {
    include ROOTDIR.DIRECTORY_SEPARATOR.'env.php';
} else {
    die("no env file detected");
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
