<?php

require_once "../../config-legacy.php";
require_once "../../system.php";

global $link;
session_start();
$mid = '011';
$mtitle = '';

if (!isset($_SESSION['userid'])) {
    header('Location: ./../../login.php');
    exit();
}

if (!isset($_SESSION['submissionID'])) {
    header('Location: ./../index.php');
    exit();
}


//-----------------------------------------------------------------------
// Get SUB folder (submission ID, mechanism CODE and ID from SESSION variables)
//-----------------------------------------------------------------
//session_start();
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
$subDir = $submissionID . "_" . $mid_padded . "_" .$user ;
//-----------------------------------------------------------------------

//$subData = "/var/www/projects/f25-01/html/files/submissions/" . $subDir . "/";
$subData = $prefix . "/files/submissions/" . $subDir . "/";

$mid = $mechanismCode;
$mtitle = $mechanismTitle;

// run java code
//------------------------------------------------------------

// Use the absolute path to java to be safe (type 'which java' in terminal to find yours)
// Common paths: /usr/bin/java or /usr/local/bin/java
$javaBin = "/usr/bin/java"; 

//$subrun = "/var/www/projects/f25-01/html/cgi-bin/core-s/m-" . $mid_padded . "/ m". $mid_padded;
$subrun =  $prefix . "/cgi-bin/core-s/m-" . $mid_padded . "/ m". $mid_padded;

// Construct the command
// 2>&1 redirects error messages to the standard output so PHP captures them
$command = "$javaBin -classpath $subrun $subData 2>&1";

// Execute
$output = shell_exec($command);

// Print output for debugging
echo "<pre>$output</pre>";

//------------------------------------------------------------

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>RUN: Memory Allocation</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/p/f25-01/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../styles/styles.css">
    <link rel="stylesheet" href="./styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script type="module" src='main.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- allows mid to be accessible to all js files -->
    <script>
        var mid = '<?php echo $mid; ?>';

        // For hrefs in the site (i.e. navigation).
        <?php
          $SITE_ROOT=$env['SITE_ROOT'];
          $httpcore_a_IO = $SITE_ROOT . "files/submissions/" . $subDir;
          $httpcore_s = $httpcore_a_IO;
        ?>

        var httpcore_a_IO = '<?php echo $httpcore_a_IO; ?>';
        var httpcore_s = '<?php echo $httpcore_s; ?>';
    </script>
    <script type="module" src="main.js" defer></script>
</head>

<body>

    <?php include '../../navbar.php'; ?>

    <div class="center text-center">
        <h1 id="title">RUN: Memory Allocation - <?= $mtitle ?> (SubID=<?= $submissionID ?>) </h1>
    </div>

    <div id="overlay">
        <div id="text">
            Loading output data... <br>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <div id="animNavBtns" class="text-center align-items-center justify-content-center">
        <button type="button" class="btn btn-primary" id="exit" onclick="location.href='../edit.php';">Exit</button>
        <button type="button" class="btn btn-primary" id="skipBack">Reset</button>
        <button type="button" class="btn btn-primary" id="stepBack">
            < Step</button>
                <button type="button" class="btn btn-primary" id="play">Play</button>
                <button type="button" class="btn btn-primary" id="stepForward">Step ></button>
                <button type="button" class="btn btn-primary" id="skipForward">End</button>
    </div>

    <div id="animarea">
        <span id="animSnLeft">Processes</span>
        <span id="animSnRight">Memory Slots</span>
        <!-- Processes -->
        <div id="procsarea">
            <div id="p1">
                <div id="p1-inside" class="align-items-center justify-content-center">
                    <span id="p1span">P1: </span>
                    <span id="p1hide" class="badge badge-light">0</span>
                </div>
            </div>
            <div id="p2">
                <div id="p2-inside" class="align-items-center justify-content-center">
                    <span id="p2span">P2: </span>
                    <span id="p2hide" class="badge badge-light">0</span>
                </div>
            </div>
            <div id="p3">
                <div id="p3-inside" class="align-items-center justify-content-center">
                    <span id="p3span">P3: </span>
                    <span id="p3hide" class="badge badge-light">0</span>
                </div>
            </div>
        </div>
        <!-- Slots -->
        <div id="memdiv">
            <table id="memtbl" style="width:180px">
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr id="m1" class="mTR">
                    <td id="m1span" class="m123span">M1:</td>
                    <td id="m1slot"></td>
                </tr>
                <tr id="m2" class="mTR">
                    <td id="m2span" class="m123span">M2:</td>
                    <td id="m2slot"></td>
                </tr>
                <tr id="m3" class="mTR">
                    <td id="m3span" class="m123span">M3:</td>
                    <td id="m3slot"></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>