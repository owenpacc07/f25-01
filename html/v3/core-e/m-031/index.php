<?php
// VIEW MODE VERSION
require_once "../../system.php";
require_once "../../config-legacy.php";
global $link;
session_start();
$mid = '031';
$compare = 'file';
$mtitle = '';



// get mechanism title
if ($mid) {
    $sql = "SELECT algorithm FROM mechanisms WHERE client_code = {$mid}";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $mtitle = $row['algorithm'];
}


$javaCommand = "java -classpath /var/www/p/f25-01/html/cgi-bin/core-e/m-031 m031 " . escapeshellarg($path);



?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>File Allocation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="./styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- allows mid to be accessible to all js files -->
    <script>
        var mid = `<?php echo $mid; ?>`;
        var httpcore_IO = `<?php echo $httpcore_IO; ?>`;
        var httpcore = `<?php echo $httpcore; ?>`;
        var compare = "<?php echo $compare; ?>"; // add this line to go to the get-io-compare
    </script>
    <script type="module" src="main.js" defer></script>
</head>

<body>
    <?php include '../../navbar.php'; ?>

    <br>
    <div class="d-flex align-items-center justify-content-center">
        <h1 id="title">031 File Allocation Contiguous</h1>
    </div>
    <div class="d-flex align-items-center justify-content-center">
        <h4 id="description">In this scheme, each file occupies a contiguous set of blocks on the disk.</h4><br>
    </div>

    <div id="overlay">
        <div id="text">
            Loading output data... <br>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div class="text-center align-items-center justify-content-center">
        <input type="button" class="btn btn-primary" value="Reset" id="reset">
        <input type="button" class="btn btn-primary" value="< Step" id="back">
        <input type="button" class="btn btn-primary" value="Play" id="play">
        <input type="button" class="btn btn-primary" value="Step >" id="next">
        <input type="button" class="btn btn-primary" value="End" id="end">
    </div>
    <div id="animarea">
        <!-- data -->
        <div id="dataarea">
            <span id="step">Step: 0</span>
            <div id="directory">
                <div id="step-dir-inside" class="d-flex align-items-center justify-content-center">
                    <span id="step-dir-inside-span">Directory</span>
                </div>
                <table id="dir">
                    <tr>
                        <th id="file">File</th>
                        <th id="start">Start</th>
                        <th id="end">End</th>
                    </tr>
                </table>
            </div>
        </div>
        <!-- Files -->
        <div id="filearea">
            <!-- <span id="step" style="display: inline; float: left; margin: 190px 0px 0px -280px;">name</span>
            <table id='fileblock' style="border: 1px solid;display: inline; float: left; margin: 220px 0px 0px -280px;">
                <tr>
                    <th style="width: 30px; height: 30px; background-color: green;border-right: 1px solid;"></th>
                    <th style="width: 30px; height: 30px; background-color: green;border-right: 1px solid;"></th>
                </tr>
            </table> -->
        </div>
        <div id="diskarea">
            <!-- <img src="./cyl.png"  style="display: inline; float: right; margin: 10px 50px 0px 0px; width: 280px;"/> -->
            <table id="disktable">
                <tr>
                    <td id="1">1</td>
                    <td id="2">2</td>
                    <td id="3">3</td>
                    <td id="4">4</td>
                </tr>
                <tr>
                    <td id="5">5</td>
                    <td id="6">6</td>
                    <td id="7">7</td>
                    <td id="8">8</td>
                </tr>
                <tr>
                    <td id="9">9</td>
                    <td id="10">10</td>
                    <td id="11">11</td>
                    <td id="12">12</td>
                </tr>
                <tr>
                    <td id="13">13</td>
                    <td id="14">14</td>
                    <td id="15">15</td>
                    <td id="16">16</td>
                </tr>
                <tr>
                    <td id="17">17</td>
                    <td id="18">18</td>
                    <td id="19">19</td>
                    <td id="20">20</td>
                </tr>
                <tr>
                    <td id="21">21</td>
                    <td id="22">22</td>
                    <td id="23">23</td>
                    <td id="24">24</td>
                </tr>
                <tr>
                    <td id="25">25</td>
                    <td id="26">26</td>
                    <td id="27">27</td>
                    <td id="28">28</td>
                </tr>
                <tr>
                    <td id="29">29</td>
                    <td id="30">30</td>
                    <td id="31">31</td>
                    <td id="32">32</td>
                </tr>
            </table>
        </div>
    </div>
    <!-- Load Chatbot Assistant -->
    <?php
    // Include chatbot loader
    $version_path = "/v3";
    $SITE_ROOT = isset($SITE_ROOT) ? $SITE_ROOT : "/p/f25-01";
    include __DIR__ . "/../../chatbot/chatbot-loader.php";
    ?>
</body>

</html>
