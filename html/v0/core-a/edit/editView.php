<?php

session_start();

$mID = $_SESSION['mechanismid'];

$place = "../../../files/core-a/m-" . $mID . "/";
$ifile = $place . "in-" . $mID . ".dat";
$ofile = $place . "out-" . $mID . ".dat";
$format = $place . "format-" . $mID . ".txt";

if (isset($_POST['submit'])) {
            $filename = $ifile;
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);

            $filename = $ofile;
            $newData = $_POST['output'];
            file_put_contents($filename, $newData);

            $filename = $format;
            $newData = $_POST['formatting'];
            file_put_contents($filename, $newData);

        header('Location: ../m-' . $mID);
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
    ?>

    <div class="section" style="display: inline;">
        <div>
            <form action="editView.php" method="POST">
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
                            <textarea name="formatting" id="formatting" cols="10" rows="10" class="textarea"><?php echo htmlentities($formatting); 
                                                                                                        ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit" name="submit">Edit Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
</body>

</html>