<?php
require(__DIR__ . "/../config.php");

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");

// get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['mechanism'])) {
        $mID = $_GET['mechanism'];
        // three digits only
        $pattern = "/(\d\d\d){1}/";

        if (preg_match($pattern, $mID) == 0) {
            echo json_encode(array("error" => "Incorrect mechanism ID."));
            return;
        }

        // *** core-s path, matching your Java workingDirectory ***
        $baseDir      = ROOT_DIR . "/files/core-s/m-$mID";
        $inputPath    = $baseDir . "/in-$mID.dat";
        $outputPath   = $baseDir . "/out-$mID.dat";

        // open input
        $fileInput = @fopen($inputPath, "r") or die(json_encode(array(
            "error" => "Input File not found",
            "path"  => $inputPath
        )));
        $inputContents = fread($fileInput, filesize($inputPath));
        fclose($fileInput);

        // open output (Java must have run first)
        $fileOutput = @fopen($outputPath, "r") or die(json_encode(array(
            "error" => "Output File not found",
            "path"  => $outputPath
        )));
        $outputContents = fread($fileOutput, filesize($outputPath));
        fclose($fileOutput);

        echo json_encode(array(
            "mechanism" => $mID,
            "input"     => $inputContents,
            "output"    => $outputContents,
        ));
        return;
    } else {
        echo json_encode(array("error" => "Mechanism ID not specified"));
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
