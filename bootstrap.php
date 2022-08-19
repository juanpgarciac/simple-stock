<?php

define('ROOTDIR', __DIR__);

define('BOOTSTRAPDIR', ROOTDIR.DIRECTORY_SEPARATOR.'bootstrap');

define('CONFIGDIR', ROOTDIR.DIRECTORY_SEPARATOR.'config');

define('SRCDIR', ROOTDIR.DIRECTORY_SEPARATOR.'src');

define('TESTSRESOURCESDIR', ROOTDIR.DIRECTORY_SEPARATOR.'tests/resources');

$_boostrap_includes = [
    BOOTSTRAPDIR => ['env.php','global.php','autoload.php','app.php'],
];

foreach ($_boostrap_includes as $dir => $files) {
    foreach ($files as $file) {
        $filepath =  $dir.DIRECTORY_SEPARATOR.$file;
        if (file_exists($filepath)) {
            include $filepath;
        }
    }
}

runApp();
