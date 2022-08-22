<?php


if (!defined('ROOTDIR')) {
    define('ROOTDIR', __DIR__.'\\..');
}


set_include_path(SRCDIR);

spl_autoload_extensions('.php');

spl_autoload_register(function ($className) {
    $file = SRCDIR . DIRECTORY_SEPARATOR . $className . '.php';
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
    if (file_exists($file)) {
        include_once $file;
    }
});
