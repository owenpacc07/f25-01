<?php

// the path to the in/out directory
$mid = '007';
$path = realpath("../../../files/core-s/m-$mid");

$output = file_get_contents("$path/out-$mid.dat");
$input = file_get_contents("$path/in-$mid.dat");
$format = file_get_contents("$path/format-$mid.txt");

$saveMessage = '';

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
        mysqli_query($link, $submission_update);

        // Check which button was clicked
        if ($_POST['submit'] === 'proceed') {
            // Redirect the user to view and run page for the mechanism
            header("Location: ./");
            exit();
        } else {
            // Save Data button - stay on page and show success message
            $saveMessage = '<div class="alert alert-success text-center" role="alert">Data saved successfully!</div>';
            // Reload the saved data
            $output = file_get_contents("$path/out-$mid.dat");
            $input = file_get_contents("$path/in-$mid.dat");
        }

    } else {
        $saveMessage = '<div class="alert alert-danger text-center" role="alert">Error: ' . mysqli_error($link) . '</div>';
    }
}

?>

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>007 Edit Data</title>
    <link rel="icon" href="/p/s23-01/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        /* Main header styling */
        #description {
            font-weight: bold;
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
        }

        /* Box headers styling */
        label {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 10px;
            display: block;
            text-align: center;
        }

        /* Custom Code Submission Box Styling */
        .form-control {
            border: 2px solid #007bff;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        /* Input textarea - dark theme with green text */
        #input {
            background-color: #1e1e1e;
            color: #4ec9b0;
        }

        #input:focus {
            background-color: #1e1e1e;
            color: #4ec9b0;
            border-color: #007acc;
            box-shadow: 0 0 8px rgba(0,122,204,0.5);
            outline: none;
        }

        /* Output textarea - dark navy theme with cyan text */
        #output {
            background-color: #0e1621;
            color: #9cdcfe;
        }

        #output:focus {
            background-color: #0e1621;
            color: #9cdcfe;
            border-color: #007acc;
            box-shadow: 0 0 8px rgba(0,122,204,0.5);
            outline: none;
        }

        /* Format textarea - light theme */
        #format {
            background-color: #f3f4f6;
            color: #374151;
        }

        /* Button group styling */
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .container {
            max-width: 1200px;
        }

        /* Center the textareas within their columns */
        .row {
            display: flex;
            justify-content: center;
        }

        .col-md-4 {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Custom Java Code Submission Box */
        #codeSubmissionSection {
            margin-top: 40px;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        #codeSubmissionSection h5 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.3rem;
            color: #2c3e50;
        }

        #javaCode {
            width: 100%;
            min-height: 300px;
            background-color: #282c34;
            color: #abb2bf;
            border: 2px solid #61afef;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            padding: 18px;
            resize: vertical;
            transition: all 0.3s ease;
        }

        #javaCode:focus {
            background-color: #282c34;
            color: #abb2bf;
            border-color: #98c379;
            box-shadow: 0 0 10px rgba(152,195,121,0.4);
            outline: none;
        }

        #javaCode::placeholder {
            color: #5c6370;
            opacity: 0.8;
        }

        /* Alert message styling */
        .alert {
            margin: 20px auto;
            max-width: 600px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
        }

        .alert-success {
            background-color: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>

<body>
    <?php
        include '../../navbar.php';
    ?>

    <br>
    <br>
    <br>

    <?php echo $saveMessage; ?>

    <form action="edit.php" method="POST">
        <div class="container">
            <div class="field is-grouped">
                <div class="control" style="width: 100%;">
                    <h4 id="description">CPU Scheduling - Edit Data</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="input">Input:</label>
                    <textarea class="form-control" name="input" id="input" rows="10"><?php echo htmlentities($input); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label for="output">Output:</label>
                    <textarea class="form-control" name="output" id="output" rows="10"><?php echo htmlentities($output); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label for="format">Format:</label>
                    <textarea readonly class="form-control" name="format" id="format" rows="10"><?php echo htmlentities($format); ?></textarea>
                </div>
            </div>
            <div class="field is-grouped">
                <div class="control button-group">
                    <button class="btn btn-success" type="submit" name="submit" value="save">Save Data</button>
                    <button class="btn btn-primary" type="submit" name="submit" value="proceed">Proceed to View</button>
                    <a class="btn btn-secondary" href="../../core-s">Go Back</a>
                </div>
            </div>

            <!-- Custom Java Code Submission Section -->
            <div id="codeSubmissionSection">
                <h5>Custom Java Code Submission</h5>
                <label for="javaCode">Enter your custom Java code here:</label>
                <textarea class="form-control" name="javaCode" id="javaCode" rows="15" placeholder="// Enter your Java code here...
public class CustomScheduler {
    public static void main(String[] args) {
        // Your code implementation
    }
}"></textarea>
            </div>
        </div>
    </form>

</body>

</html>
<body>
