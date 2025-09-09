<?php
require(__DIR__ . "/../config.php");

header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['compare'])) {
        $cID = $_GET['compare'];

        $inputPath = ROOT_DIR . "/files/core-c/c-$cID/in-$cID.dat";
        $outputPath = ROOT_DIR . "/files/core-c/c-$cID/out-$cID.dat";
        if (isset($_GET['mechanism'])) {
            $mID = $_GET['mechanism']; // e.g., '031', '032', '033'
            $outputPath = ROOT_DIR . "/files/core-c/m-$mID/out-$mID.dat";
            
        } else {
            echo json_encode(array("error" => "mechanism ID not specified"));
        }

        $fileInput = fopen($inputPath, "r") or die(json_encode(array("error" => "Input File not found: $inputPath")));
        $inputContents = fread($fileInput, filesize($inputPath));

        $fileOutput = fopen($outputPath, "r") or die(json_encode(array("error" => "Output File not found: $outputPath")));
        $outputContents = fread($fileOutput, filesize($outputPath));

        echo json_encode(array(
            "compare" => $cID,
            "mechanism" => $mID,    
            "input" => $inputContents,
            "output" => $outputContents,
            "output_file" => basename($outputPath)
        ));

        fclose($fileInput);
        fclose($fileOutput);
        return;
    } else {
        echo json_encode(array("error" => "compare ID not specified"));
        return;
    }
}

if ($method == 'POST') {
    echo "THIS IS A POST REQUEST";
}
if ($method == 'PUT') {
    echo "THIS IS A PUT REQUEST";
}
if ($method == 'DELETE') {
    echo "THIS IS A DELETE REQUEST";
}
?>