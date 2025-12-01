<?php
//This file contains variables of commonly used file paths--use these variables throughout the code so that
//if the file paths need to change, everything is updated here at once
$env = parse_ini_file(__DIR__ . "/../../.env");

// For hrefs in the site (i.e. navigation).
$SITE_ROOT=$env['SITE_ROOT'];

// For input/output paths. Will be a path to html folder
$ROOT_DIR = realpath(__DIR__ . '/..');

//change this prefix to match current class key
$prefix = "/var/www/p/f25-01/html";

//navbar location
$navbar = __DIR__ . "./navbar.php";

//change this var to match current version
$version_path = "/v5";


//-----------------------------------------------------------------------
// Get SUB folder (submission ID, mechanism CODE and ID from SESSION variables)
//-----------------------------------------------------------------
session_start();
$user = $_SESSION['userid'];
$submissionID = $_SESSION['submissionID'];
$mechanismID = $_SESSION['mechanismID'];
$mechanismCode = $_SESSION['mechanismCode'];
$mechanismTitle = $_SESSION['mechanismTitle'];
$submissionFolder= $_SESSION['submissionFolder'];
//-----------------------------------------------------------------
$submission_id = $submissionID;
$mid_padded = str_pad($mechanismCode, 3, '0', STR_PAD_LEFT);

//SUB FOLDER
//$sub_base = "/files/submissions/";
$subDir = $submissionID . "_" . $mid_padded . "_" .$user ;
//-----------------------------------------------------------------------



//java code location
$cgibin_core = $ROOT_DIR . "/cgi-bin/core";
//$cgibin_core = $prefix . "/cgi-bin/core";

//java code advanced location
$cgibin_core_a = $ROOT_DIR . "/cgi-bin/core-a";
//$cgibin_core_a = $prefix . "/cgi-bin/core-a";

//view mode I/O files
$io_files = $ROOT_DIR . "/files/core";
//$io_files = $prefix . "/files/core";

//SUB mode I/O files
//$io_files_s = $ROOT_DIR . "/files/submissions/" . $subDir;

$io_files_s = $ROOT_DIR . "/files/core-s";
//$io_files_s = $prefix . "/files/core-s";

//advanced mode I/O files
$io_files_a = $ROOT_DIR . "/files/core-a";
//$io_files_a = $prefix . "/files/core-a";


//php files for CPU core
$cpu_core = $prefix . $version_path . "/core";

//php files for CPU core advanced
$cpu_core_a = $prefix . $version_path . "/core-a";

//http request files
$httpcore_IO = $SITE_ROOT . "/files/core";

//htpp core-a
$httpcore_a_IO = $SITE_ROOT . "files/core-a";

//Dynamically defined
//htpp core-s
$httpcore_s_IO = $SITE_ROOT . "files/submissions/" . $subDir;

$httpcore = $SITE_ROOT . $version_path . "/core";

$httpcore_a = $SITE_ROOT . $version_path . "/core-a";

$httpcore_c = $SITE_ROOT . $version_path . "/core-c";

$httpcore_e = $SITE_ROOT . $version_path . "/core-e";

// SUB
$httpcore_s = $ROOT_DIR . "/files/submissions/" . $subDir;

?>
