<?php

/* Environment and Constants initialization */
$_environment_pool = [];

/**
 * @param array $set
 *
 * @return array
 */
function envPool(array $set = []): array|null
{
    global $_environment_pool;
    foreach ($set as $key => $value) {
        $_environment_pool[$key] = $value;
    }
    return $_environment_pool;
}


/**
 * Retrieve all environment variables required for the project.
 * Following this order:
 * 1. User defined bootstrap constants
 * 2. Defined on config/env.php
 * 3. Defined on /.env file
 * 4. Defined within the system environment
 *
 * @return void
 */
function bootEnvironment(): void
{
    //get and set env variables from defined constants
    envPool(get_defined_constants(true)['user']);

    //then take env variables from config/env.php
    envPool(arrayFromFile(path(CONFIG_DIR, 'env.php')));

    //Then if .env file exists take env variables from it
    if (is_file(path(ROOT_DIR, '.env'))) {
        if ($envFromFile = parse_ini_file(path(ROOT_DIR, '.env'))) {
            //get and set env variables from .env file
            envPool($envFromFile);
        }
    } else {
        trigger_error("No environment file detected (.env)", E_USER_WARNING);
    }
    //And last, replace any env set with the system environment
    //(e.g phpunit <php><env> elements or with the putenv function)
    envPool(array_intersect_key(getenv(null, true), envPool()));
}

bootEnvironment();
