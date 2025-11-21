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
$flagPath = realpath("../../../files/core-c/c-disk/flag-file.txt");
file_put_contents($flagPath, "0");



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
    <title>Disk Scheduling Comparison</title>
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
                ['Algorithm', 'Distance Traveled'],
                ['FCFS', 0], ['SSTF', 0], ['CSCAN', 0], ['LOOK', 0], ['CLOOK', 0]
            ]);
            const options = {
                chart: { title: 'Disk Scheduling Algorithms Comparison' },
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
            const dataArray = [['Algorithm', 'Distance Traveled', { role: 'style' }]]; //role'style' should change the colors for each column?

            for (const [algorithm, faults] of Object.entries(simulationResults)) {
                dataArray.push([algorithm, faults, randomColor]); // randomColor here should change the color of each column but doesnt?

            }
            // Set chart options
            const data = google.visualization.arrayToDataTable(dataArray);
            const options = {
                chart: { title: 'Disk Scheduling Algorithm Comparison' },
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
                        <h3 class="text-center">Disk Scheduling Comparison</h3>
                    </div>
                    <div class="card-body">

                        <p class="text-center">Input Data:</p>
                        <!-- Form to input the page reference string -->
                        <form method="post" class="text-center">
                            <div class="form-group">
                                <label for="requestInput">Request Queue (comma-separated):</label>
                                <input type="text" name="requests" class="form-control" id="requestInput"
                                    value="<?php echo isset($_POST['requests']) ? htmlspecialchars($_POST['requests']) : (isset($_GET['input']) ? htmlspecialchars($_GET['input']) : '98, 183, 37, 122, 14, 124, 65, 67'); ?>"
                                    required>
				<label for="headInput"> Head:</label>
                                <input type="text" name="head" class="form-control" id="headInput"
                                    value="<?php echo isset($_POST['head']) ? htmlspecialchars($_POST['head']) : (isset($_GET['head']) ? htmlspecialchars($_GET['head']) : '50'); ?>"
                                    required>
				<label for="diskSizeInput">Disk Size (comma-separated):</label>
                                <input type="text" name="diskSize" class="form-control" id="diskSizeInput"
                                    value="<?php echo isset($_POST['diskSize']) ? htmlspecialchars($_POST['diskSize']) : (isset($_GET['diskSize']) ? htmlspecialchars($_GET['diskSize']) : '400'); ?>"
                                    required>

                            </div>
                            <button type="submit" name="compare" class="btn btn-purple d-inline mr-2">Simulate for
                                Results</button>
                            <button type="button" class="btn btn-purple d-inline mr-2" onclick="showChart(event)">Show
                                Chart</button>
			   <button type="submit" name="compare" class="btn btn-purple d-inline mr-2"> Randomize Inputs
                                     </button>
                            <!-- <button type="button" class="btn btn-purple d-inline" onclick="saveResults()">Save result</button>-->
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
        // Convert the pages input to an array of integers
        $requests = isset($_POST['requests']) ? array_map('intval', explode(',', $_POST['requests'])) : [];
	$head = isset($_POST['head']) ? intval($_POST['head']) : 0;
        $diskSize = isset($_POST['diskSize']) ? intval($_POST['diskSize']) : 400;


	$line1 = "0 $diskSize";
	$line2 = $head;
	$line3 = implode(' ', $requests);
	$newContents = "$line1\n$line2\n$line3";

	$filePath = realpath("../../../files/core-c/c-disk/in-001.dat");
        file_put_contents($filePath, $newContents);

	$filePath = realpath("../../../files/core-c/c-disk/out-001.dat");
        file_put_contents($filePath, $newContents);

	$filePath = realpath("../../../files/core-c/c-disk/out-041.dat");
        file_put_contents($filePath, $newContents);

	$filePath = realpath("../../../files/core-c/c-disk/out-042.dat");
        file_put_contents($filePath, $newContents);

	$filePath = realpath("../../../files/core-c/c-disk/out-043.dat");
        file_put_contents($filePath, $newContents);

	$filePath = realpath("../../../files/core-c/c-disk/out-044.dat");
        file_put_contents($filePath, $newContents);

	$filePath = realpath("../../../files/core-c/c-disk/out-045.dat");
        file_put_contents($filePath, $newContents);







	$filePath = realpath("../../../files/core-c/m-041/in-041.dat");
	file_put_contents($filePath, $newContents);
	$filePath = realpath("../../../files/core-c/m-042/in-042.dat");
	file_put_contents($filePath, $newContents);
	$filePath = realpath("../../../files/core-c/m-043/in-043.dat");
	file_put_contents($filePath, $newContents);
	$filePath = realpath("../../../files/core-c/m-044/in-044.dat");
	file_put_contents($filePath, $newContents);
	$filePath = realpath("../../../files/core-c/m-045/in-045.dat");
	file_put_contents($filePath, $newContents);






        // Define your page replacement algorithms
        $algorithms = [
            'FCFS' => 'fcfsAlgorithm',
            'SSTF' => 'sstfAlgorithm',
            'CSCAN' => 'cscanAlgorithm',
            'LOOK' => 'lookAlgorithm',
            'CLOOK' => 'clookAlgorithm'
        ];


	$links = [
	'FCFS' => '../../core-c/m-041',
  	'SSTF' => '../../core-c/m-042',
        'CSCAN' => '../../core-c/m-043',
        'LOOK' => '../../core-c/m-044',
   	'CLOOK' => '../../core-c/m-045'
	];


	// set flage file to 1 (input ready to read)
        $flagPath = realpath("../../../files/core-c/c-disk/flag-file.txt");
        file_put_contents($flagPath, "1");


        // Run the algorithms on the pages array
        $results = [];
        foreach ($algorithms as $name => $function) {
            // Call the function dynamically based on algorithm name
            $results[$name] = $function($requests, $head, $diskSize);
        }


	$filePath = realpath("../../../files/core-c/c-disk/out-041.dat");
	$line3 = $results["FCFS"];
        file_put_contents($filePath, "$line1\n$line2\n$line3");

	$filePath = realpath("../../../files/core-c/c-disk/out-042.dat");
        $line3 = $results["SSTF"];
        file_put_contents($filePath, "$line1\n$line2\n$line3");

	$filePath = realpath("../../../files/core-c/c-disk/out-043.dat");
        $line3 = $results["CSCAN"];
        file_put_contents($filePath, "$line1\n$line2\n$line3");

	$filePath = realpath("../../../files/core-c/c-disk/out-044.dat");
        $line3 = $results["LOOK"];
        file_put_contents($filePath, "$line1\n$line2\n$line3");

	$filePath = realpath("../../../files/core-c/c-disk/out-045.dat");
        $line3 = $results["CLOOK"];
        file_put_contents($filePath, "$line1\n$line2\n$line3");



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
                        <h3 class="text-center">Disk Scheduling Results</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Algorithm</th>
                                    <th>Distance Traveled</th>
                                    <th>Input</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (!empty($results)): ?>
                                    <?php foreach ($results as $algorithm => $faults): ?>
                                        <tr>


					<td>
                        <a href="<?php echo $links[$algorithm]; ?>" target = "_blank"  class="btn btn-purple btn-sm" onclick="writeInputToFile();">
                            <?php echo htmlspecialchars($algorithm); ?>
                        </a>
                    </td>


					    <td><?php echo htmlspecialchars($faults); ?></td>
                                            <td><?php echo htmlspecialchars(implode(', ', $requests)); ?></td>
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


    // Function to implement FCFS (First Come, First Serve) disk scheduling algorithm
    function fcfsAlgorithm($requests, $head, $diskSize)
    {
    	$totalDistance = 0; // Track total distance traveled
   	$currentPosition = $head; // Start from the initial head position

	// Loop through each request in order
	foreach ($requests as $request) {
		$totalDistance += abs($request - $currentPosition); // Calculate movement distance
	        $currentPosition = $request; // Move head to new position
	}

    	return $totalDistance; // Return total distance traveled
    }


function sstfAlgorithm($requests, $head, $diskSize)
{
    $totalDistance = 0;
    $currentPosition = $head;
    $remaining = $requests; // copy of the requests array

    while (count($remaining) > 0) {
        // Find the closest request
        $closestIndex = 0;
        $closestDistance = abs($remaining[0] - $currentPosition);

        for ($i = 1; $i < count($remaining); $i++) {
            $distance = abs($remaining[$i] - $currentPosition);
            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestIndex = $i;
            }
        }

        // Move the head to the closest request and add to total
        $totalDistance += $closestDistance;
        $currentPosition = $remaining[$closestIndex];

        // Remove the served request
        unset($remaining[$closestIndex]);

        // Reindex the array so the next loop runs smoothly
        $remaining = array_values($remaining);
    }


    return $totalDistance;

}


// Bubble sort for sorting arrays
function bubbleSort($arr) {
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - 1 - $i; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                $temp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $temp;
            }
        }
    }

    return $arr;
}

function cscanAlgorithm($requests, $head, $diskSize) {
    $sorted = bubbleSort($requests);

    // Separate into "upper" (>= head) and "lower" (< head)
    $upper = array();
    $lower = array();
    for ($i = 0; $i < count($sorted); $i++) {
        if ($sorted[$i] >= $head) {
            $upper[] = $sorted[$i];
        } else {
            $lower[] = $sorted[$i];
        }
    }

    $distance = 0;
    $current = $head;

    // 1) Serve the upper part in ascending order
    for ($i = 0; $i < count($upper); $i++) {
        $distance += abs($upper[$i] - $current);
        $current = $upper[$i];
    }

    // 2) If there’s anything in the lower part, jump back to 0
    if (count($lower) > 0) {
        $distance += abs($current - 0);
        $current = 0;

        // 3) Serve the lower part (which is sorted ascending, so from smallest up)
        for ($i = 0; $i < count($lower); $i++) {
            $distance += abs($lower[$i] - $current);
            $current = $lower[$i];
        }
    }

    return $distance;
}

function lookAlgorithm($requests, $head, $diskSize) {
    $sorted = bubbleSort($requests);

    // Split into left (< head) and right (>= head)
    $left = array();
    $right = array();
    for ($i = 0; $i < count($sorted); $i++) {
        if ($sorted[$i] < $head) {
            $left[] = $sorted[$i];
        } else {
            $right[] = $sorted[$i];
        }
    }

    $left = bubbleSort($left);
    $reversedLeft = array();
    for ($i = count($left) - 1; $i >= 0; $i--) {
        $reversedLeft[] = $left[$i];
    }

    $distance = 0;
    $current = $head;

    // 1) Serve left (descending)
    for ($i = 0; $i < count($reversedLeft); $i++) {
        $distance += abs($reversedLeft[$i] - $current);
        $current = $reversedLeft[$i];
    }

    // 2) Then serve right (ascending)
    for ($i = 0; $i < count($right); $i++) {
        $distance += abs($right[$i] - $current);
        $current = $right[$i];
    }


    return $distance;
}

function clookAlgorithm($requests, $head, $diskSize) {
    $sorted = bubbleSort($requests);

    // Split into upper (>= head) and lower (< head)
    $upper = array();
    $lower = array();
    for ($i = 0; $i < count($sorted); $i++) {
        if ($sorted[$i] >= $head) {
            $upper[] = $sorted[$i];
        } else {
            $lower[] = $sorted[$i];
        }
    }

    $distance = 0;
    $current = $head;

    // 1) Serve upper in ascending order
    for ($i = 0; $i < count($upper); $i++) {
        $distance += abs($upper[$i] - $current);
        $current = $upper[$i];
    }

    // 2) If there's a "lower" set, jump directly from the highest upper to the lowest lower
    if (count($lower) > 0) {
        $distance += abs($current - $lower[0]);
        $current = $lower[0];

        // 3) Then serve the rest of lower (still ascending)
        for ($i = 1; $i < count($lower); $i++) {
            $distance += abs($lower[$i] - $current);
            $current = $lower[$i];
        }
    }




    return $distance;
}















?>

</body>

<style>
    /* Button styles to match purple theme */
    .btn-purple {
        background-color: #9769D9;
        color: white;
        border-radius: 8px;
        /* Rounded corners for buttons */
    }

    .btn-purple:hover {
        background-color: #B594E4;
        /* Lighter purple for hover effect */
        color: white;
    }

    /* Customize the background color of the table card header */
    .card-header {
        background-color: #9769D9;
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
        background-color: #B594E4;
        color: white;
        font-weight: bold;
    }

    .table th:nth-child(3) {
        background-color: #C6A4F1;
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
