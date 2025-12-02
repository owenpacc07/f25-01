<?php

// This is the main config file to be imported by all other files. This file will contain all global variables and functions that are used in multiple files. This is mostly a rewrite of the existing config.php file, which has been renamed to config-legacy.php. 

// This file is for placing any functions that are used in multiple files. This file should only be directly imported by config.php.
require __Dir__ . "/config/functions.php";

// This file is for placing any global variables that are used in multiple files. This file should only be directly imported by config.php.
require __Dir__ . "/config/vars.php";

// This is the environment file. This will contain ONLY info that is machine specific, and should not be committed to the repo. This file should only be directly imported by config files.
$env = parse_ini_file(__DIR__."/../../.env");

/* Database credentials. */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', $env['DB_USERNAME']);
define('DB_PASSWORD', $env['DB_PASSWORD']);
define('DB_NAME', $env['DB_NAME']);

// This is the ONLY DB connection that should be used. 
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>
