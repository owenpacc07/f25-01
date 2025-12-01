<?php
    require_once "../system.php";

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

    // $_POST is associative array - key is json string, value is null
    $post = json_decode(array_keys($_POST)[0]);

    $mid = $post->mechanism;
    $type = $post->type;
    

    // Read flag-file
    if ($type == 0) {  
        $myfile = fopen("$ROOT_DIR/files/submissions/$subDir/flag-file.txt", "r") or die("Unable to open file!");
        //$myfile = fopen("$ROOT_DIR/files/core-a/m-$mid/flag-file.txt", "r") or die("Unable to open file!");
        $filecontent = fgets($myfile);
        echo $filecontent;
    }

    // Reset flag-file
    if ($type == 1) {
        $myfile = fopen("$ROOT_DIR/files/submissions/$subDir/flag-file.txt", "w") or die("Unable to open file!");
        //$myfile = fopen("$ROOT_DIR/files/core-a/m-$mid/flag-file.txt", "w") or die("Unable to open file!");
        fwrite($myfile, 0)."\n";
        echo "flag-file reset to 0";
    }

    fclose($myfile);

?>
