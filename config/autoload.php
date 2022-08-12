<?php 

if(!defined('ROOTDIR'))
    define('ROOTDIR',__DIR__.'\\..');


spl_autoload_register(function($className) {
	$file = ROOTDIR . '\\src\\' . $className . '.php';
	$file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
	echo $file;
	if (file_exists($file)) {
		include $file;
	}
});