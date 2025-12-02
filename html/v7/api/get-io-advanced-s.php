<?php
require(__DIR__ . "/../config.php");

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
$this_sub_base = "/files/submissions/". $submissionID . "_" . $mid_padded . "_" .$user ."/";
//-----------------------------------------------------------------------


header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

// get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
	if (isset($_GET['mechanism'])) {
		$mID = $_GET['mechanism'];
		$pattern = "/(\d\d\d){1}/";

		if (preg_match($pattern, $mID) == 0) {
			echo json_encode(array("error" => "Incorrect mechanism ID."));
			return;
		}

/*
		$fileInput = fopen(ROOT_DIR . "/files/core-a/m-$mID/in-$mID.dat", "r") or die(json_encode(array("error" => "Input File not found")));
		$inputContents = fread($fileInput, filesize(ROOT_DIR . "/files/core-a/m-$mID/in-$mID.dat"));

		$fileOutput = fopen(ROOT_DIR . "/files/core-a/m-$mID/out-$mID.dat", "r") or die(json_encode(array("error" => "Output File not found")));
		$outputContents = fread($fileOutput, filesize(ROOT_DIR . "/files/core-a/m-$mID/out-$mID.dat"));
*/

$this_sub_base = "/files/submissions/". $submissionID . "_" . $mid_padded . "_" .$user ;

		$fileInput = fopen(ROOT_DIR . "$this_sub_base/in-$mID.dat", "r") or die(json_encode(array("error" => "Input File not found")));
		$inputContents = fread($fileInput, filesize(ROOT_DIR . "$this_sub_base/in-$mID.dat"));

		$fileOutput = fopen(ROOT_DIR . "$this_sub_base/out-$mID.dat", "r") or die(json_encode(array("error" => "Output File not found")));
		$outputContents = fread($fileOutput, filesize(ROOT_DIR . "$this_sub_base/out-$mID.dat"));

		echo json_encode(array(
			"mechanism" => $mID,
			"input" => $inputContents,
			"output" => $outputContents,
		));

		fclose($fileInput);
		return;
	} else {
		echo json_encode(array("error" => "Mechanism ID not specified "));
		return;
	}
}

// other request types can be implemented
if ($method == 'POST') {
	echo "THIS IS A POST REQUEST";
}
if ($method == 'PUT') {
	echo "THIS IS A PUT REQUEST";
}
if ($method == 'DELETE') {
	echo "THIS IS A DELETE REQUEST";
}
