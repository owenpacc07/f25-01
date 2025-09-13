<?php

require_once "../../config-legacy.php";
global $link;
session_start();
$mid = $_POST['mechanismid'];
$mtitle = '';
// get mechanism title
if ($mid) {
    $sql = "SELECT algorithm FROM mechanisms WHERE client_code = {$mid}";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $mtitle = $row['algorithm'];
}
// run java code
shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-{$mid} m{$mid}");
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- allows mid to be accessible to js files -->
    <script>
        var mid = `<?php echo $mid; ?>`;
    </script>
    <script type="module" src="main.js" defer></script>
</head>

<body>
    <?php include '../corenavbar.php'; ?>

    <div id="title-content" class="d-flex align-items-center justify-content-center">
        <h1 id="title"><?= $mtitle ?></h1>
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
    </div>

    <div id="animNavBtns" class="d-flex flex-row justify-content-center">
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

</body>

</html>