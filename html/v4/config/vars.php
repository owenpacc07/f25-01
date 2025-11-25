<?php

// This is a file for placing any global variables that are used in multiple files. This file should only be directly imported by config.php. Note that this file is mostly a rewrite system.php file.

/* Global variables are going to be set up with the define function, which is a PHP function that allows you to define a constant. This is a good way to set up global variables because it prevents them from being changed later in the code. This is similar to Java's final keyword. The syntax is as follows:

define('NAME_OF_VARIABLE', value); */

// This is the environment file. This will contain ONLY info that is machine specific, and should not be committed to the repo. This file should only be directly imported by config files.
$env = parse_ini_file(__DIR__."/../../../.env");

/* Version. Should be v1, v2, etc.
Change this to match current version */
define('VERSION', 'v2');

// For hrefs in the site (i.e. navigation).
define('SITE_ROOT', $env['SITE_ROOT'] . '/' . VERSION);

// For input/output paths. Will be a path to html folder
define('ROOT_DIR', realpath(__DIR__ . '/../..'));

//Navbar location
define('NAVBAR', __DIR__ . "/../navbar.php");

//java code location
define('CGIBIN_CORE', ROOT_DIR . "/cgi-bin/core");

//java code advanced location
define('CGIBIN_CORE_A', ROOT_DIR . "/cgi-bin-a/core");

//view mode I/O files
define('IO_FILES', ROOT_DIR . "/files/core");

//advanced mode I/O files
define('IO_FILES_A', ROOT_DIR . "/files/core-a");

//php files for CPU core
define('CPU_CORE', ROOT_DIR . VERSION . "/core");

//php files for CPU core advanced

define('CPU_CORE_A', ROOT_DIR . VERSION . "/core-a");

//http request files
$noversion = $env['SITE_ROOT'];
define('HTTPCORE_IO', $noversion . "/files/core");

//htpp core-a
define('HTTPCORE_A_IO', $noversion . "/files/core-a");

define('HTTPCORE', SITE_ROOT . "/core");

define('HTTPCORE_A', SITE_ROOT . "/core-a");
