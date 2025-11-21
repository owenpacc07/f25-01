<?php

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

$env = parse_ini_file(__DIR__."/../../.env");
/* This file is for connecting the the SQL database. This database holds the table to store any admin username and passwords.
   The Database credentials will likely need to be updated*/

/* Database credentials. */

define('DB_SERVER', 'localhost');
define('DB_USERNAME', $env['DB_USERNAME']);
define('DB_PASSWORD', $env['DB_PASSWORD']);
define('DB_NAME', $env['DB_NAME']);
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>
