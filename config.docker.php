<?php
function read_secret($name) {
    if ($val = getenv($name)) {
        return $val;
    }

    if ($envFile = getenv($name . '_FILE')) {
        return trim(file_get_contents($envFile));
    }

    trigger_error("Missing '$name' configuration.", E_USER_ERROR);
}

$_CONFIG = array(
	'hostname' => getenv('DB_HOST'),
	'username' => getenv('DB_USER'),
	'password' => read_secret('DB_PASS'),
	'database' => getenv('DB_NAME'),
	'persistent' => 0,
	'driver' => 'mysqli',
	'code' => read_secret('APP_KEY'),
);
