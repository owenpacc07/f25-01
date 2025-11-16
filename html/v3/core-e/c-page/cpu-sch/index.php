<?php


// Start a session to store the success message
session_start();

// Check if there is a success or error message
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the success message after it's displayed
} elseif (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the error message after it's displayed
}

// Define the path for the page reference files
$mid = '003';
$path = realpath("../../../files/core-c/m-$mid");

// Load current input and output data
$output = file_get_contents("$path/out-$mid.dat");
$input = file_get_contents("$path/in-$mid.dat");

if (isset($_POST['save_results'])) {

    // When the form is submitted, write the user input and output data to respective files

    // Write user's input data to the input file
    $filenameIN = "$path/in-$mid.dat";
    $newDataIN = $_POST['input'];
    file_put_contents($filenameIN, $newDataIN);

    // Write user's output data to the output file
    $filenameOUT = "$path/out-$mid.dat";
    $newDataOUT = $_POST['output'];
    file_put_contents($filenameOUT, $newDataOUT);

    // Include the 'config.php' file to establish a database connection
    require_once './../../config.php';
    // Start a new session
    session_start();
    // Get the user ID from the session
    $user = $_SESSION['userid'];

    // Query the database to get the mechanism_id (you could use $mid here or get from the database)
    $mechanism = mysqli_fetch_all(mysqli_query($link, "SELECT mechanism_id FROM comparisons WHERE client_code=$mid"))[0][0];

    // Declare the restrict_view variable (set it based on your logic)
    $restrict_view = 1;

    // Get the code file path
    $codeFilePath = realpath("../../../cgi-bin/core-c/m-$mid/");

    // Create the query to insert submission data into the database
    $experiment_insert = "INSERT INTO experiments (family_id, user_id, input_path, output_path, code_path, restrict_view) 
                          VALUES ($mechanism, $user, '$filenameIN', '$filenameOUT', '$codeFilePath', $restrict_view);";

    // Insert the data and set success message if successful
    if (mysqli_query($link, $experiment_insert)) {
        $_SESSION['success_message'] = "Submission successful. Results have been saved!";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($link);
    }

    // Create a new folder in the submissions directory to store the user's input and output
    $experiment_id = mysqli_insert_id($link);
    $experiment_path = "../../../files/experiments/" . $experiment_id . "_" . $mechanism . "_" . $user;
    mkdir($experiment_path, 0770);
    chown($experiment_path, 'nobody');

    // Copy the user's input and output to the new folder
    copy($filenameIN, "$experiment_path/in-$mid.dat");
    copy($filenameOUT, "$experiment_path/out-$mid.dat");

    // update the filenameIN and filenameOUT to the new paths
    $filenameIN = "$experiment_path/in-$mid.dat";
    $filenameOUT = "$experiment_path/out-$mid.dat";

    // Create a submission query to edit the submission with the new paths
    $experiment_update = "UPDATE experiments SET input_path='$filenameIN', output_path='$filenameOUT' WHERE experiment_id=$experiment_id;";

    // Redirect to the index page with updated input and output data
    header("Location: index.php?input=" . urlencode($newDataIN) . "&output=" . urlencode($newDataOUT));
    exit();  // Always call exit after redirect
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPU Scheduling Comparison</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['bar'] });

        // Callback function to initialize the chart
        google.charts.setOnLoadCallback(initializeChart);

        let chart;
        let simulationResults; // Placeholder for results from PHP

        // Create an array to store the colors
        const color = [];
        const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'); // Random number between 0xFFFFFF
        color.push(randomColor);  // Store the random color

        function initializeChart() {
            const data = google.visualization.arrayToDataTable([
                ['Mechanism', 'Average Waiting Time', 'Average Turn Around Time'],
                ['FCFS', 0, 0], ['Non-SJF', 0, 0], ['Non-High', 0, 0], ['Non-Low', 0, 0], ['RoundRobin', 0, 0], ['Pre-SJF', 0, 0], ['Pre-High', 0, 0], ['Pre-Low', 0, 0]
            ]);
            const options = {
                chart: { title: 'CPU Scheduling Algorithms Comparison' },
                legend: { position: 'bottom' },
                colors: color // Random color
            };
            chart = new google.charts.Bar(document.getElementById('draw-chart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        // Function to display the chart after form submission
        function showChart(event) {
            event.preventDefault();

            // Message if a user clicks on Show Chart without running a simulation
            if (typeof simulationResults === 'undefined' || Object.keys(simulationResults).length === 0) {
                alert('Please Run The Simulation First!');
                return; // Exit

                document.getElementById('draw-chart').style.display = 'block';
            }

            // Convert simulationResults to chart data
            // And also pick the colors for the bars
            const dataArray = [['Mechanism', 'Average Waiting Time', 'Average Turn Around Time', { role: 'style' }]]; //role'style' should change the colors for each column?

            // Loop through simulationResults to add each algorithm data to the chart data array
            for (const [algorithm, times] of Object.entries(simulationResults)) {
                const averageWaitingTime = times.averageWaitingTime;
                const averageTurnaroundTime = times.averageTurnaroundTime;

                // Push data for each algorithm into dataArray
                dataArray.push([algorithm, averageWaitingTime, averageTurnaroundTime, randomColor]);

            }            // Set chart options
            const data = google.visualization.arrayToDataTable(dataArray);
            const options = {
                chart: { title: 'CPU Scheduling Algorithms Comparison' },
                legend: { position: 'bottom' },
                colors: color // Random Color, but for the whole graph(all columns)
            };

            // Draw the chart
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        function saveResults() {
            document.getElementById("saveForm").submit();  // This submits the form for saving results
            //alert('Submission successful. Results have been saved!');
        }

    </script>
</head>

<body>
    <!-- Include navbar -->
    <?php include realpath('../../navbar.php'); ?>


    <!-- Main container for input and simulation -->
    <div class="container mt-5">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">CPU Scheduling Comparison</h3>
                    </div>
                    <div class="card-body">

                        <p class="text-center">Input Data:</p>
                        <!-- Form to input the burst time for processes string -->
                        <form method="post" class="text-center">
                            <div class="form-group">
                                <label for="pageInput">Edit Input Data-Enter Process Burst Times
                                    (comma-separated):</label>
                                <input type="text" name="processes" class="form-control" id="pageInput"
                                    value="<?php echo isset($_POST['processes']) ? htmlspecialchars($_POST['processes']) : (isset($_GET['input']) ? htmlspecialchars($_GET['input']) : '5, 4, 3, 4'); ?>"
                                    required>
                            </div>
                            <button type="submit" name="compare" class="btn btn-green d-inline mr-2">Simulate for
                                Results</button>
                            <button type="button" class="btn btn-green d-inline mr-2" onclick="showChart(event)">Show
                                Chart</button>
                            <!-- <button type="button" class="btn btn-green d-inline" onclick="saveResults()">Save result</button>-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Hidden form for saving results (triggered by the "Save result" button) -->
    <form id="saveForm" method="post">
        <input type="hidden" name="input"
            value="<?php echo isset($_GET['input']) ? htmlspecialchars($_GET['input']) : ''; ?>">
        <input type="hidden" name="output"
            value="<?php echo isset($_GET['output']) ? htmlspecialchars($_GET['output']) : ''; ?>">
        <input type="hidden" name="save_results" value="1">
    </form>


    <?php
    // Check if form is submitted and handle the logic
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['compare'])) {
        // Convert the processes input to an array of integers
        $processes = isset($_POST['processes']) ? array_map('intval', explode(',', $_POST['processes'])) : [];

        // Define cpu scheduling algorithms
        $algorithms = [
            'FCFS' => 'fcfsAlgorithm',
            'Non-SJF' => 'nonsjfAlgorithm',
            'Non-High' => 'nonhighAlgorithm',
            'Non-Low' => 'nonlowAlgorithm',
            'RoundRobin' => 'robinAlgorithm',
            'Pre-SJF' => 'presjfAlgorithm',
            'Pre-High' => 'prehighAlgorithm',
            'Pre-Low' => 'prelowAlgorithm'
        ];

        // Run the algorithms on the processes array
        $results = [];
        foreach ($algorithms as $name => $function) {
            // Call the function dynamically based on algorithm name
            $results[$name] = $function($processes);
        }

        // Pass the results to JavaScript for visualization
        echo "<script>simulationResults = " . json_encode($results) . ";</script>";
    }
    ?>

    <!-- Results and Chart Side by Side -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <!-- Results Table Column -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">CPU Scheduling Results</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Algorithm</th>
                                    <th>Waiting Time</th>
                                    <th>Turn Around Time</th>
                                    <th>Input</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (!empty($results)): ?>
                                    <?php foreach ($results as $algorithm => $times): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($algorithm); ?></td>
                                            <td>
                                                <?php
                                                // Access waitingTime from the $times array
                                                if (isset($times['waitingTime'])) {
                                                    echo htmlspecialchars($times['averageWaitingTime']); // Display waiting time
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                // Access turnaroundTime from the $times array
                                                if (isset($times['turnaroundTime'])) {
                                                    echo htmlspecialchars($times['averageTurnaroundTime']); // Display turnaround time
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars(implode(', ', $processes)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Please Run a Simulation to See Results.</td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Chart Column -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div id="draw-chart" style="width: 100%; height: 323px; display: block;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php ?>

    <?php

    //I think we need to add priorities to the functions below?
    //Something like this >> function fcfsAlgorithm($processes, $priorities)
    //Calculates turn around time at the moment
    
    function fcfsAlgorithm($processes)
    {
        // Number of processes
        $n = count($processes);

        // Initialize arrays to hold the waiting times and turnaround times
        $waitingTime = array_fill(0, $n, 0); // Default waiting time for all processes is 0
        $turnaroundTime = array_fill(0, $n, 0); // Default turnaround time for all processes is 0
    
        // Calculate waiting time
        for ($i = 1; $i < $n; $i++) {
            $waitingTime[$i] = $waitingTime[$i - 1] + $processes[$i - 1];
        }

        // Calculate turnaround time
        for ($i = 0; $i < $n; $i++) {
            $turnaroundTime[$i] = $waitingTime[$i] + $processes[$i];
        }

        // Calculate total waiting time and total turnaround time for average calculation
        $totalWaitingTime = array_sum($waitingTime);
        $totalTurnaroundTime = array_sum($turnaroundTime);

        // Calculate average waiting time and turnaround time
        $averageWaitingTime = $totalWaitingTime / $n;
        $averageTurnaroundTime = $totalTurnaroundTime / $n;

        // Return the result(s)
        return [
            'waitingTime' => $waitingTime,
            'turnaroundTime' => $turnaroundTime,
            'averageWaitingTime' => $averageWaitingTime,
            'averageTurnaroundTime' => $averageTurnaroundTime
        ];
    }

    function nonSJFAlgorithm($processes)
    {

    }
    function nonhighAlgorithm($processes)
    {

    }

    function nonlowAlgorithm($processes)
    {


    }

    function robinAlgorithm($processes)
    {


    }

    function presjfAlgorithm($processes)
    {


    }

    function prehighAlgorithm($processes)
    {


    }

    function prelowAlgorithm($processes)
    {


    }



    ?>
</body>

<style>
    /* Button styles to match green theme */
    .btn-green {
        background-color: #99C653;
        color: white;
        border-radius: 8px;
        /* Rounded corners for buttons */
    }

    .btn-green:hover {
        background-color: #B8DB8A;
        /* Lighter green for hover effect */
        color: white;
    }

    /* Customize the background color of the table card header */
    .card-header {
        background-color: #6DA335;
        color: white;
        font-weight: bold;
    }

    /* Add a subtle shadow to the card */
    .card {
        border-radius: 12px;
        /* Rounding corners of the card */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        /* Soft shadow */
        overflow: hidden;
        /* Ensures content doesn't overflow rounded corners */

    }

    .card:hover {
        transform: translateY(-5px);
        /* Slight upward movement */
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        /* Deeper shadow on hover */
    }

    .table th,
    .table td {
        text-align: center;
        border-bottom: 1px solid #ddd;
        /* Soft bottom borders */
    }

    /* Customize the table header background color */
    .table th {
        background-color: #99C653;
        color: white;
        font-weight: bold;
    }

    .table th:nth-child(4) {
        background-color: #B8DB8A;
        /* Different color for 'Input' column */

    }

    /* Input field for page reference string */
    input[type="text"] {
        border-radius: 8px;
        /* Rounded corners for input fields */
        padding: 8px;
        /* Add some internal padding */
        border: 2px solid #ddd;
        /* Soft border */
    }

    /* Add a little padding inside the form */
    form {
        padding: 8px;
    }
</style>

</html>