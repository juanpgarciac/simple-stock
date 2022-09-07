<?php

function bootAutoload()
{
    set_include_path(env('SRC_DIR'));

    spl_autoload_extensions('.php');

    spl_autoload_register(function ($className) {
        $file = path(env('SRC_DIR'), $className . '.php');
        $file = str_replace('\\', slash(), $file);
        if (is_file($file)) {
            include_once $file;
        } else {
            $file = path(env('TESTS_RESOURCES_DIR'), 'src', $className . '.php');
            $file = str_replace('\\', slash(), $file);
            if (is_file($file)) {
                include_once $file;
            }
        }
    });
}


bootAutoload();
