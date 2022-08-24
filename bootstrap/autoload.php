<?php

function bootAutoload()
{
    set_include_path(env('SRC_DIR'));

    spl_autoload_extensions('.php');
    
    spl_autoload_register(function ($className) {
        $file = env('SRC_DIR') . DIRECTORY_SEPARATOR . $className . '.php';
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
        if (is_file($file)) {
            include_once $file;
        }
    });
}


bootAutoload();
