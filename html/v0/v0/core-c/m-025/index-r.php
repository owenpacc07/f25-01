<?php

// Page Replacement
require_once "../../system.php";
require_once "../../config-legacy.php";
global $link;
session_start();

$mid = '025';
$mtitle = '(Your Mechanism Title)';

// get mechanism title based on mid
$sql = "SELECT algorithm FROM mechanisms WHERE client_code = {$mid}";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);
$mtitle = $row['algorithm'];

shell_exec("java -classpath {$cgibin_core_a}/m-{$mid} m{$mid}");


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Replacement</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../styles/styles.css">
    <link rel="stylesheet" href="./styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- allows mid to be accessible to all js files -->
    <script>
        var mid = `<?php echo $mid; ?>`;
        var httpcore_a_IO = `<?php echo $httpcore_a_IO; ?>`;
        var httpcore_a = `<?php echo $httpcore_a; ?>`;
    </script>
    <script type="module" src="main.js" defer></script>
</head>

<body>

    <?php include '../../navbar.php'; ?>
    <br>
    <div id="title-content" class="d-flex align-items-center justify-content-center">
        <h1 id="title">RUN: Page Replacement - <?= $mtitle ?></h1>
    </div>

    <div id="overlay">
        <div id="text">
            Loading output data... <br>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Table for input data -->
    <div id="input">
        <span id="input_label">Input Data: </span>
        <table id="input_table">
            <tr id="input_table_row">
            </tr>
        </table>
        <span></span>
    </div>


    <!-- Animation control buttons -->
    <div id="animNavBtns" class="d-flex flex-row justify-content-center">
        <div id="title-content" class="d-flex align-items-center justify-content-center">
            <button type="button" class="btn btn-primary" id="exit" onclick="location.href='index.php';">Exit</button>
        </div>
         <button type="button" class="btn btn-primary" id="skipBack">Reset</button>
        <button type="button" class="btn btn-primary" id="stepBack">
            < Step</button>
                <button type="button" class="btn btn-primary" id="play">Play</button>
                <button type="button" class="btn btn-primary" id="stepForward">Step ></button>
                <button type="button" class="btn btn-primary" id="skipForward">End</button>
    </div>

    <!-- Aniamtion result -->
    <div id="output">
        <table id="output_table">
        </table>
    </div>

    <!-- Extra info -->
    <div id="info">
        <div id="faultCounter">
            <span id="faultCounter_label">Number of faults: </span>
            <span id="faultCounter_value">0</span>
        </div>
    </div>
    <div id="info">
        <div id="info_table_container">
            <table id="info_table">
                <thead id="info_table_header">
                    <tr>
                        <td>Previous Pages</td>
                        <td>Times Used</td>
                    </tr>
                </thead>
                <tbody id="info_table_body"></tbody>
            </table>
        </div>
    </div>

</body>

</html>
