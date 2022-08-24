<?php

/* Environment and Constants initialization */
$_environment_pool = [];

/**
 * @param array $set
 * 
 * @return array
 */
function envPool(array $set = [] ):array|null
{    
    global $_environment_pool;
    foreach ($set as $key => $value) {
        $_environment_pool[$key] = $value;
    }
    return $_environment_pool;
}


function bootEnvironment():void
{
    if (is_file(path(ROOT_DIR,'.env'))) {
        //get and set env variables from defined constants
        envPool(get_defined_constants(true)['user']);
        
        if ($env = parse_ini_file(path(ROOT_DIR,'.env'))) {
            //get and set env variables from .env file
            envPool($env);
        }   
    } else {
        trigger_error("No environment file detected (.env)", E_USER_ERROR);
    }    
}

bootEnvironment();