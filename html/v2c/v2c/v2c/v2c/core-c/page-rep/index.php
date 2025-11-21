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
    <title>Page Replacement Comparison</title>
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
                ['Mechanism', 'Number of Page Faults'],
                ['FIFO', 0], ['LRU', 0], ['OPT', 0], ['MFU', 0], ['LFU', 0]
            ]);
            const options = {
                chart: { title: 'Page Replacement Algorithms Comparison' },
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
            const dataArray = [['Mechanism', 'Number of Page Faults', { role: 'style' }]]; //role'style' should change the colors for each column?

            for (const [algorithm, faults] of Object.entries(simulationResults)) {
                dataArray.push([algorithm, faults, randomColor]); // randomColor here should change the color of each column but doesnt?

            }
            // Set chart options
            const data = google.visualization.arrayToDataTable(dataArray);
            const options = {
                chart: { title: 'Page Replacement Algorithms Comparison' },
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
                        <h3 class="text-center">Page Replacement Comparison</h3>
                    </div>
                    <div class="card-body">

                        <p class="text-center">Input Data:</p>
                        <!-- Form to input the page reference string -->
                        <form method="post" class="text-center">
                            <div class="form-group">
                                <label for="pageInput">Edit Input Data-Page Reference String (comma-separated):</label>
                                <input type="text" name="pages" class="form-control" id="pageInput"
                                    value="<?php echo isset($_POST['pages']) ? htmlspecialchars($_POST['pages']) : (isset($_GET['input']) ? htmlspecialchars($_GET['input']) : '7, 0, 1, 2, 4, 1, 3, 4, 2, 3, 0, 3, 2, 1, 2, 0'); ?>"
                                    required>
                            </div>
                            <button type="submit" name="compare" class="btn btn-purple d-inline mr-2">Simulate for
                                Results</button>
                            <button type="button" class="btn btn-purple d-inline mr-2" onclick="showChart(event)">Show
                                Chart</button>
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
        $pages = isset($_POST['pages']) ? array_map('intval', explode(',', $_POST['pages'])) : [];

        // Define your page replacement algorithms
        $algorithms = [
            'FIFO' => 'fifoAlgorithm',
            'LRU' => 'lruAlgorithm',
            'OPT' => 'optAlgorithm',
            'MFU' => 'mfuAlgorithm',
            'LFU' => 'lfuAlgorithm'
        ];

        // Run the algorithms on the pages array
        $results = [];
        foreach ($algorithms as $name => $function) {
            // Call the function dynamically based on algorithm name
            $results[$name] = $function($pages);
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
                        <h3 class="text-center">Page Replacement Results</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Algorithm</th>
                                    <th>Number of Faults</th>
                                    <th>Input</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (!empty($results)): ?>
                                    <?php foreach ($results as $algorithm => $faults): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($algorithm); ?></td>
                                            <td><?php echo htmlspecialchars($faults); ?></td>
                                            <td><?php echo htmlspecialchars(implode(', ', $pages)); ?></td>
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
    // Function to implement FIFO (First In, First Out) page replacement algorithm
    function fifoAlgorithm($pages)
    {
        $frameCount = 3;  // Define the number of frames available in memory
        $memory = [];  // Initialize an empty array to represent memory
        $faults = 0;  // Counter for page faults

        // Loop through each page reference
        foreach ($pages as $page) {
            // If the page is not already in memory, we have a page fault
            if (!in_array($page, $memory)) {
                // If memory has space, just add the page
                if (count($memory) < $frameCount) {
                    $memory[] = $page;
                } else {
                    // If memory is full, remove the oldest page (FIFO) and add the new one
                    array_shift($memory);
                    $memory[] = $page;
                }
                $faults++;  // Increment page fault counter
            }
        }
        // Return the number of page faults
        return $faults;
    }

    // Function to implement LRU (Least Recently Used) page replacement algorithm
    function lruAlgorithm($pages)
    {
        $frameCount = 3;  // Define the number of frames available in memory
        $memory = [];  // Initialize an empty array to represent memory
        $faults = 0;  // Counter for page faults

        // Loop through each page reference
        foreach ($pages as $page) {
            // If the page is not in memory, we have a page fault
            if (!array_key_exists($page, $memory)) {
                // If memory is full, remove the least recently used page
                if (count($memory) >= $frameCount) {
                    array_shift($memory);
                }
                // Add the new page to memory and mark it as recently used
                $memory[$page] = true;
                $faults++;  // Increment page fault counter
            } else {
                // If the page is in memory, remove it and re-add it to mark it as recently used
                unset($memory[$page]);
                $memory[$page] = true;
            }
        }
        // Return the number of page faults
        return $faults;
    }

    // Function to implement OPT (Optimal) page replacement algorithm
    function optAlgorithm($pages)
    {
        $frameCount = 3;  // Define the number of frames available in memory
        $memory = [];  // Initialize an empty array to represent memory
        $faults = 0;  // Counter for page faults

        // Loop through each page reference
        for ($i = 0; $i < count($pages); $i++) {
            $page = $pages[$i];
            // If the page is not already in memory, we have a page fault
            if (!in_array($page, $memory)) {
                // If memory has space, just add the page
                if (count($memory) < $frameCount) {
                    $memory[] = $page;
                } else {
                    // If memory is full, find the page to replace (the one that will not be used for the longest time in the future)
                    $farthestUseIndex = -1;
                    $pageToReplace = -1;
                    foreach ($memory as $memPage) {
                        // Find when the page will be used again in the future
                        $nextUse = array_search($memPage, array_slice($pages, $i + 1));
                        if ($nextUse === false) {
                            $pageToReplace = $memPage;
                            break;  // This page is not used again, so replace it
                        } else {
                            // If the page will be used later, find which one is used the farthest
                            if ($nextUse > $farthestUseIndex) {
                                $farthestUseIndex = $nextUse;
                                $pageToReplace = $memPage;
                            }
                        }
                    }
                    // Remove the page to be replaced and add the new page
                    $memory = array_diff($memory, [$pageToReplace]);
                    $memory[] = $page;
                }
                $faults++;  // Increment page fault counter
            }
        }
        // Return the number of page faults
        return $faults;
    }

    // Function to implement MFU (Most Frequently Used) page replacement algorithm
    function mfuAlgorithm($pages)
    {
        $frameCount = 3;  // Define the number of frames available in memory
        $memory = [];  // Initialize an empty array to represent memory
        $faults = 0;  // Counter for page faults

        // Loop through each page reference
        foreach ($pages as $page) {
            // If the page is already in memory, increment its frequency count
            if (array_key_exists($page, $memory)) {
                $memory[$page]++;
            } else {
                // If memory has space, add the page with a frequency of 1
                if (count($memory) < $frameCount) {
                    $memory[$page] = 1;
                } else {
                    // If memory is full, find the most frequent page and replace it
                    $mostFrequentPage = array_keys($memory, max($memory))[0];
                    unset($memory[$mostFrequentPage]);
                    $memory[$page] = 1;
                }
                $faults++;  // Increment page fault counter
            }
        }
        // Return the number of page faults
        return $faults;
    }

    // Function to implement LFU (Least Frequently Used) page replacement algorithm
    function lfuAlgorithm($pages)
    {
        $frameCount = 3;  // Define the number of frames available in memory
        $memory = [];  // Initialize an empty array to represent memory
        $faults = 0;  // Counter for page faults

        // Loop through each page reference
        foreach ($pages as $page) {
            // If the page is already in memory, increment its frequency count
            if (array_key_exists($page, $memory)) {
                $memory[$page]++;
            } else {
                // If memory is full, remove the least frequent page
                if (count($memory) >= $frameCount) {
                    $leastFrequent = array_keys($memory, min($memory))[0];
                    unset($memory[$leastFrequent]);
                }
                // Add the new page to memory with a frequency of 1
                $memory[$page] = 1;
                $faults++;  // Increment page fault counter
            }
        }
        // Return the number of page faults
        return $faults;
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