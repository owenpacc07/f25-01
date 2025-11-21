<?php

// the path to the in/out directory
$mid = '013';
$path = realpath("../../../files/core-c/m-$mid");

if (isset($_POST['submit'])) {
    if (empty($_POST['input'])) {
    } else {
        //$filename = "$path/in-$mid.dat";
        //$newData = $_POST['input'];
        //file_put_contents($filename, $newData);
	
	//write user's input data to the input file
        $filenameIN = "$path/in-$mid.dat";
        $newDataIN = $_POST['input'];
        file_put_contents($filenameIN, $newDataIN);

	//write user's output data to the output file
        $filenameOUT = "$path/out-$mid.dat";
        $newDataOUT = $_POST['output'];
        file_put_contents($filenameOUT, $newDataOUT);

    }

    // redirects to the normal page
    $loc = "Location: ./";
    header($loc);
}

?>

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>011 Edit Data</title>
    <link rel="icon" href="/p/s23-01/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <?php include '../../navbar.php'; ?>

    <br>
    <br>
    <br>

    <?php
    $path = realpath("../../../files/core-c/m-$mid");
    $output = file_get_contents("$path/out-$mid.dat");
    $input = file_get_contents("$path/in-$mid.dat");
    $format = file_get_contents("$path/format-$mid.txt");
    ?>

    <form action="edit.php" method="POST">
        <div class="container">
            <div class="row">
                <div class="field is-grouped">
                    <div class="control">
                        <h4 id="description">011 Edit Data</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="textarea1">Input:</label>
                    <textarea class="form-control" name="input" id="input" rows="10"><?php echo htmlentities($input); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label for="textarea2">Output:</label>
                    <textarea class="form-control" name="output" id="output" rows="10"><?php echo htmlentities($output); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label for="textarea2">Format:</label>
                    <textarea readonly class="form-control" name="format" id="format" rows="10"><?php echo htmlentities($format); ?></textarea>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button class="btn btn-primary" type="submit" name="submit">Save</button>
                        <button class="btn btn-primary" type="submit" name="submit" href=$path>Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</body>

</html>
<body>
