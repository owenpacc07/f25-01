<?php

// the path to the in/out directory
$mid = '041';
$path = realpath("../../../files/core-s/m-$mid");

$output = file_get_contents("$path/out-$mid.dat");
$input = file_get_contents("$path/in-$mid.dat");
$format = file_get_contents("$path/format-$mid.txt");




if (isset($_POST['submit'])) {

    //When we write the user's input and output data, we also declare variables to be used in the SQL query below

    //write user's input data to the input file
    $filenameIN = "$path/in-$mid.dat";
    $newDataIN = $_POST['input'];
    file_put_contents($filenameIN, $newDataIN);

    //write user's output data to the output file
    $filenameOUT = "$path/out-$mid.dat";
    $newDataOUT = $_POST['output'];
    file_put_contents($filenameOUT, $newDataOUT);

    // Include the 'config.php' file to establish a database connection
    require_once './../../config.php';
    // Start a new session
    session_start();
    // Get the user ID from the session
    $user = $_SESSION['userid'];
    
    //Query the database "mechanisms" table to get the mechanism_id from the client code, which is $mid
    $mechanism = mysqli_fetch_all(mysqli_query($link, "select mechanism_id from mechanisms where client_code=$mid"))[0][0];
   
    // Declare the restrict_view variable
    $restrict_view = 1;
    
    // Get the code file path
    $codeFilePath = realpath("../../../cgi-bin/core-s/m-$mid/");

    
    // Create a submission query to send to the database
    $submission_insert = "INSERT INTO submissions (mechanism_id, user_id, input_path, output_path, code_path, restrict_view) VALUES ($mechanism, $user,'$filenameIN','$filenameOUT','$codeFilePath', $restrict_view);";

    // Send the submission query to the database, redirect user to submission page if successful
    if (mysqli_query($link, $submission_insert)) {
        echo "Submission successful.";

	// Create a new folder in the submissions directory to store the user's input and output
        $submission_id = mysqli_insert_id($link);
        $submission_path = "../../../files/submissions/" . $submission_id . "_" . $mechanism . "_" . $user;
        mkdir($submission_path, 0770);
        chown($submission_path, 'nobody');

        // Copy the user's input and output to the new folder
        copy($filenameIN, "$submission_path/in-$mid.dat");
        copy($filenameOUT, "$submission_path/out-$mid.dat");

        // update the filenameIN and filenameOUT to the new paths
        $filenameIN = "$submission_path/in-$mid.dat";
        $filenameOUT = "$submission_path/out-$mid.dat";

        // Create a submission query to edit the submission with the new paths
        $submission_update = "UPDATE submissions SET input_path='$filenameIN', output_path='$filenameOUT' WHERE submission_id=$submission_id;";

        
    } else {
        echo "Error: " . mysqli_error($link);
    }

    // Redirect the user to view and run page for the mechanism (..)
    header("Location: ./");

}

?>

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>041 Edit Data</title>
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
    $path = realpath("../../../files/core-s/m-$mid");
    $output = file_get_contents("$path/out-$mid.dat");
    $input = file_get_contents("$path/in-$mid.dat");
    $format = file_get_contents("$path/format-$mid.txt");
    ?>

    <form action="edit.php" method="POST">
        <div class="container">
            <div class="row">
                <div class="field is-grouped">
                    <div class="control">
                        <h4 id="description">041 Edit Data</h4>
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
                        <button class="btn btn-primary" type="submit" name="submit">Submit Data and Proceed to View</button>
                        <button class="btn btn-primary" type="submit" name="submit" href=$path>Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</body>

</html>
<body>
