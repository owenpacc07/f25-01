<?php
//These edit data files can probably be modified to just use one for all mechanisms
session_start();
require_once "../../system.php";

$mID = $_SESSION['mechanismid'];
//$mID = "000";

$place = $io_files_a . "/m-" . $mID . "/";
$ifile = $place . "in-" . $mID . ".dat";
$ofile = $place . "out-" . $mID . ".dat";
$format = $place . "format-" . $mID . ".txt";

if (isset($_POST['submit'])) {
    if (isset($_POST['viz'])){
        if (empty($_POST['input'])) {
        } else {
            $filename = $ifile;
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        if (empty($_POST['output'])) {
        } else {
            $filename = $ofile;
            $newData = $_POST['output'];
            file_put_contents($filename, $newData);
        }
        header('Location: ./index.php');
    }
    if (isset($_POST['run'])) {
        $filename = $ifile;
        $newData = $_POST['input'];
        file_put_contents($filename, $newData);
        shell_exec("java -classpath $cgibin_core_a/m-{$mid} m{$mid}");
        header('Location: ./index.php');
    }
    //if neither boxex checked
    if (empty($_POST['input'])) {
    } else {
        $filename = $ifile;
        $newData = $_POST['input'];
        file_put_contents($filename, $newData);
    }
    if (empty($_POST['output'])) {
    } else {
        $filename = $ofile;
        $newData = $_POST['output'];
        file_put_contents($filename, $newData);
    }
    header('Location: ./index.php');
}
?>

<html>

<head>
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>View Files</title>
    <style>
        body {
            background-color: #F0FFF0;
        }
    </style>
</head>

<body>

    <?php
    include '../../navbar.php';

    $output = file_get_contents($ofile);
    $input = file_get_contents($ifile); 
    $formatting = file_get_contents($format);
    // $output = file($ofile);
    // $input = file($ifile);
    // $count = 0;
    // $outputStartEnds = [];
    // $outputInfo = [];
    // $outputAverages = [];
    // $isOutputInfo = false;
    // $inputInfo = [];
    // foreach ($output as $line) {
    //     if ($count >= 3) {
    //         if (empty(trim($line, " \n"))) {
    //             $isOutputInfo = true;
    //         } elseif (!$isOutputInfo) {
    //             $vals = explode(',', $line);
    //             array_push($outputStartEnds, array($vals[0], $vals[1], $vals[2]));
    //         } else {
    //             if (str_contains($line, ",")) {
    //                 $vals = explode(',', $line);
    //                 array_push($outputInfo, array($vals[0], $vals[1], $vals[2], $vals[3], $vals[4], $vals[5], $vals[6]));
    //             } else {
    //                 array_push($outputAverages, $line);
    //             }
    //         }
    //     }
    //     $count++;
    // }
    // foreach ($input as $line) {
    //     $lineData = explode('/', $line)[0];
    //     $vals = explode(' ', $lineData);
    //     array_push($inputInfo, array($vals[0], $vals[1], $vals[2], $vals[3]));
    // }
    ?>

    <div class="section" style="display: inline;">
        <div>
            <form action="editData.php" method="POST">
                <div>
                    <div class="columns">
                        <div class="field column" style="max-width: 500px;">
                            <label for="input"><span><strong>Input: </strong></span></label>
                            <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($input); 
                                                                                                    ?></textarea>
                        </div>
                        <div class="field column" style="max-width: 500px;">
                            <label for="output"><span><strong>Output: </strong></span></label>
                            <textarea name="output" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($output); 
                                                                                                        ?></textarea>
                        </div>
                        <div class="field column" style="max-width: 500px;">
                            <label for="formatting"><span><strong>Format: </strong></span></label>
                            <textarea name="formatting" id="formatting" cols="10" rows="10" class="textarea" readonly><?php echo htmlentities($formatting); 
                                                                                                        ?></textarea>
                        </div>
                    </div>
                </div>

                <div>
                        <input type="checkbox" id="viz" name="viz">
                        <label for="viz"> Visualize</label><br>
                        <input type="checkbox" id="run" name="run">
                        <label for="run"> Run and Visualize</label><br>
                </div>

                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit" name="submit">Save</button>
                        <a class="button is-link is-light" href="../../../v3/core-a/m-<?php echo $mID ?>">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
</body>

</html>