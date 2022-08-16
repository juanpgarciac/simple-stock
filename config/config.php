<?php


if (!defined('ROOTDIR')) {
    define('ROOTDIR', __DIR__.'\\..');
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

if (!function_exists('env')) {
    /**
     * Get environment value
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    function env(string $key, string|array|null $default = null): string|array|null
    {
        return getenv($key, true) ?? $default;
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    function env_str(string $key, string $default = ''): string
    {
        $env = getenv($key, true);
        if (is_array($env) && count($env)>0) {
            $env = (string) $env[0];
        }
        return $env ?? $default;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die.
     * @param mixed ...$args
     *
     * @return void
     */
    function dd(...$args)
    {
        ob_clean();
        echo '<pre>';
        foreach ($args as $arg) {
            var_dump($arg);
        }

        echo '</pre>';
        die;
    }
}
