<?php

require_once "../../config-legacy.php";
require_once "../../system.php";

global $link;
session_start();
$mid = '011';
$mtitle = '';
// get mechanism title
if ($mid) {
    $sql = "SELECT algorithm FROM mechanisms WHERE client_code = {$mid}";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $mtitle = $row['algorithm'];
}
// run java code
shell_exec("java -classpath {$cgibin_core}/m-{$mid} m{$mid}");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Memory Allocation</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
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
        var mid = `<?php echo $mid; ?>`;
        var httpcore_IO = `<?php echo $httpcore_IO; ?>`;
        var httpcore = `<?php echo $httpcore; ?>`;
    </script>
    <script type="module" src="main.js" defer></script>
</head>

<body>

    <?php include '../../navbar.php'; ?>

    <div class="center text-center">
        <h1 id="title">Memory Allocation - <?= $mtitle ?></h1>
    
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
        <button type="button" class="btn btn-primary" id="refresh">Refresh Data</button>
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