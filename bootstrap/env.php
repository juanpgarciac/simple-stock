<?php

/* Environment and Constants initialization */

if (!defined('ROOTDIR')) {
    define('ROOTDIR', __DIR__.DIRECTORY_SEPARATOR.'..');
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
