<?php


if (!defined('ROOTDIR')) {
    define('ROOTDIR', __DIR__.'\\..');
}


set_include_path(ROOTDIR.'/src/');

spl_autoload_extensions('.php');

spl_autoload_register(function ($className) {
    $file = ROOTDIR . '\\src\\' . $className . '.php';
    $file = str_replace('\\', DIRECTORY_SEPARATOR, ROOTDIR . '\\src\\' . $className . '.php');
    if (file_exists($file)) {
        include_once $file;
    }
});

