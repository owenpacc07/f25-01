<?php

require_once "../../config-legacy.php";
require_once "../../system.php";
global $link;
session_start();
$mid = '001';
$mtitle = '';
// get mechanism title
if ($mid) {
    $sql = "SELECT algorithm FROM mechanisms WHERE client_code = {$mid}";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $mtitle = $row['algorithm'];
}

// Get input file from user if there is one
// print_r($_POST);
// $userInputFile = $_POST;

// run java code
//shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-{$mid} m{$mid}");
//shell_exec("java -classpath {$cgibin_core_a}/m-{$mid} m{$mid}");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPU Scheduling</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../styles/styles.css">
    <link rel="stylesheet" type="text/css" href="./styles.css">
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


    <div id="title-content" class="d-flex align-items-center justify-content-center">
        <h1 id="title">CPU Scheduling - <?= $mtitle ?></h1>
    </div>

    <div id="overlay">
        <div id="text">
            Loading output data... <br>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Wait Time Calculations -->
        <div id="waitingTime">
            <p><span class="space"><strong>Waiting Time:</strong></span><span class="space">Exit Time</span> - <span class="space">Arrival Time</span> - <span class="space">Burst Time</span></p>
            <div id="waitContainer"></div>
            <!-- <div id="waitTimeAverage"><p id="wait_average_result"></p></div> -->
            <div id="waitTimeAverage">
                <div class="averageInformation">
                    <p class="leftEquation">Wait Average:<span class="space"> </span></p>
                    <div class="fraction">
                        <span class="numerator">Total Wait Time</span>
                        <span class="denominator">Total Number of Processes</span>
                    </div>
                </div>
                <div class="averageInformation">
                    <p id="averageWaitTimeText"></p>
                    <div class="fraction">
                        <span class="numerator" id="numeratorWait"></span>
                        <span class="denominator" id="denominatorWait"></span>
                    </div>
                    <p id="waitAverageResult" class="averageResult"></p>
                </div>
            </div>
        </div>

        <!-- Process Table -->
        <table class="tableA" id="processTable">
            <tbody id="procArea">
                <tr>
                    <th>Process ID</th>
                    <th>Arrival Time</th>
                    <th>Burst Time</th>
                    <th>Priority</th>
                </tr>
            </tbody>
        </table>

        <!-- Response Time Calculations -->
        <div id="responseTime">
            <p><span class="space"><strong>Response Time:</strong></span><span class="space">Time at which the process uses the CPU for the first time</span> - <span class="space">Arrival time</span></p>
            <div id="responseContainer"></div>
            <div id="responseTimeAverage">
                <div class="averageInformation">
                    <p class="leftEquation">Response Average:<span class="space"> </span></p>
                    <div class="fraction">
                        <span class="numerator">Total Response Time</span>
                        <span class="denominator">Total Number of Processes</span>
                    </div>
                </div>
                <div class="averageInformation">
                    <p id="averageResponseTimeText"></p>
                    <div class="fraction">
                        <span class="numerator" id="numeratorResponse"></span>
                        <span class="denominator" id="denominatorResponse"></span>
                    </div>
                    <p id="responseAverageResult" class="averageResult"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center h3">
        By Process:
    </div>
   <div id="animNavBtns" class="text-center align-items-center justify-content-center">
        <button type="button" class="btn btn-primary" id="exit" onclick="location.href='index.php';">Exit</button>
        <button type="button" class="btn btn-primary" id="skipBack">Reset</button>
        <button type="button" class="btn btn-primary" id="stepBack">
            < Step</button>
                <button type="button" class="btn btn-primary" id="play">Play</button>
                <button type="button" class="btn btn-primary" id="stepForward">Step ></button>
                <button type="button" class="btn btn-primary" id="skipForward">End</button>
     </div>

    <div id="ganttContainer" class="container">
        <gantt>
            <div class="d-flex align-items-center justify-content-center">
                <table class="tableA" id="animationResult">
                    <tbody id="holder">
                        <tr id="head"></tr>
                        <tr id="body"></tr>
                    </tbody>
                </table>
            </div>
        </gantt>
    </div>

    <hr>

   <!-- Buttons and table for indepth data -->
    <div class="text-center h3">
        By Time:
    </div>
    <div id="animNavBtns2" class="text-center align-items-center justify-content-center">
        <button type="button" class="btn btn-primary" id="skipBack2">Reset</button>
        <button type="button" class="btn btn-primary" id="stepBack2">
            < Step</button>
                <button type="button" class="btn btn-primary" id="play2">Play</button>
                <button type="button" class="btn btn-primary" id="stepForward2">Step ></button>
                <button type="button" class="btn btn-primary" id="skipForward2">End</button>
    </div>

    <br>

    <div class="container">
        <table class="tableA" id="processTable" style="width:70%">
            <tbody id="detailedOutput">
                <tr>
                    <th>Time</th>
                    <th colspan="4">Remaining Burst Time</th>
                    <th>CPU</th>
                    <th colspan="4">Waiting Time</th>
                    <th>Queue</th>
                </tr>
                <tr>
                    <th></th>
                    <th>P1</th>
                    <th>P2</th>
                    <th>P3</th>
                    <th>P4</th>
                    <th></th>
                    <th>P1</th>
                    <th>P2</th>
                    <th>P3</th>
                    <th>P4</th>
                    <th></th>
                </tr>
            </tbody>
        </table>
        <hr>
        </hr>
    </div>

</body>

</html>