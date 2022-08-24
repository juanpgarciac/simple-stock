<?php

/* Environment and Constants initialization */
$_enviroment_pool = [];

function envPool():array
{
    global $_enviroment_pool;
    return $_enviroment_pool;
}


function bootEnvironment(){
    global $_enviroment_pool;
    if (is_file(ROOT_DIR.DIRECTORY_SEPARATOR.'.env')) {
        if ($env = parse_ini_file(ROOT_DIR.DIRECTORY_SEPARATOR.'.env')) {
            //get and set env variables from .env file
            foreach ($env as $key => $value) {
                $_enviroment_pool[$key] = $value;//putenv("$key=$value");
            }
        }
        //get and set env variables from defined constants
        foreach (get_defined_constants(true)['user'] as $key => $value) {
            //putenv("$key=$value");
            $_enviroment_pool[$key] = $value;
        }
    } else {
        trigger_error("No environment file detected (.env)", E_USER_ERROR);
    }
}

bootEnvironment();