<?php

define('ROOT_DIR', __DIR__);

$_dir = ROOT_DIR.DIRECTORY_SEPARATOR;

define('BOOTSTRAP_DIR', $_dir.'bootstrap');

define('CONFIG_DIR', $_dir.'config');

define('SRC_DIR', $_dir.'src');

define('VIEWS_DIR', $_dir.'views');

define('TESTS_RESOURCES_DIR', $_dir.'tests/resources');

function boot()
{
    $_boostrap_includes = [
        //files in proper order
        BOOTSTRAP_DIR => ['global','env','autoload','app'],
    ];
    
    foreach ($_boostrap_includes as $dir => $files) {
        foreach ($files as $file) {
            $filepath =  $dir.DIRECTORY_SEPARATOR.$file.'.php';
            if (is_file($filepath)) {
                include_once $filepath;
            }
        }
    }

    runApp();
}

unset($_dir);

boot();