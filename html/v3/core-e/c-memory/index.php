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
$mid = '031';
$path = realpath("../../../files/core-e/m-$mid");

//$coreCPath = "/var/www/projects/f25-01/html/files/core-e/c-memory";
$coreCPath = realpath(__DIR__ . "/../../../files/core-e/c-memory");
$inputFile = "$coreCPath/in-memory.dat"; // Input file 

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
    $codeFilePath = realpath("../../../cgi-bin/core-e/m-$mid/");

    $compare = memory;
    
    // Create the query to insert submission data into the database
    $experiment_insert = "INSERT INTO experiments (family_id, user_id, input_path, output_path, code_path, restrict_view) 
                          VALUES ($compare, $user, '$filenameIN', '$filenameOUT', '$codeFilePath', $restrict_view);";

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
    <title>Memory Allocation Comparison</title>
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
                ['Mechanism', 'Number of Unallocated Processes'],
                ['FF', 0], ['BF', 0], ['WF', 0]
            ]);
            const options = {
                chart: { title: 'Memory Allocation Algorithms Comparison' },
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
            const dataArray = [['Mechanism', 'Number of Unallocated Processes', { role: 'style' }]]; //role'style' should change the colors for each column?

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

<script>
    // Returns a random integer between min (inclusive) and max (inclusive).
    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    
    // Creates a string of (length) random integers, with a lowest possible value of (min), largest (max)
    function genRandomInputString(length, min, max) {
        let values = [];

        for (let i = 0; i < length; i++) {
            values.push(getRandomInt(min, max));
        }

        return values.join(", ");
    }
    
    // Create random set of (1-5) processes and memory slots each. Sizes can range from 100 - 1000
    function randomizeInputs() {      
            let num = getRandomInt(1, 4);
            let numProcesses = num;
            let numMemory = num;
        
            document.getElementById("pageInput").value = genRandomInputString(numProcesses, 100, 1000);
            document.getElementById("memoryInput").value = genRandomInputString(numMemory, 100, 1000);
    }
</script>

<body>
    <!-- Include navbar -->
    <?php include realpath('../../navbar.php'); ?>


    <!-- Main container for input and simulation -->
    <div class="container mt-5">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Memory Allocation Comparison</h3>
                    </div>
                    <div class="card-body">

                        <p class="text-center">Input Data:</p>
                        <!-- Form to input the process and memory slot sizes -->
                        <form method="post" class="text-center">
                            <div class="form-group">
                                <label for="pageInput">Edit Input Data-Process Sizes (comma-separated):</label>
                                <input type="text" name="processes" class="form-control" id="pageInput"
                                    value="<?php echo isset($_POST['processes']) ? htmlspecialchars($_POST['processes']) : (isset($_GET['input']) ? htmlspecialchars($_GET['input']) : '212, 417, 112, 426'); ?>"
                                    required>
                                <label for="memoryInput">Edit Input Data-Memory Slot Sizes (comma-separated):</label>
                                <input type="text" name="memoryslots" class="form-control" id="memoryInput"
                                    value="<?php echo isset($_POST['memoryslots']) ? htmlspecialchars($_POST['memoryslots']) : (isset($_GET['input']) ? htmlspecialchars($_GET['input']) : '100, 500, 200, 300'); ?>"
                                    required>
                            </div>
                            <button type="submit" name="compare" class="btn btn-purple d-inline mr-2">Simulate for
                                Results</button>
                            <button type="button" class="btn btn-purple d-inline mr-2" onclick="showChart(event)">Show
                                Chart</button>
                            <button type="button" class="btn btn-purple d-inline mr-2" onclick="randomizeInputs();">Randomize</button>
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
    function writeInput($processes, $memoryslots) {
        // -- Write input to respective input dat file --
    
        $pCount = count($processes);
        $mCount = count($memoryslots);
    
    
    
        $file = fopen("../../../files/core-e/c-memory/in-memory.dat", "w");
        
        // # of free memory slots
        fwrite($file, $mCount . "\n");
        
        // addresses of start and end of a free memory slot
    
        $memLocation = 0;
    
        for ($j = 0; $j < $mCount; $j++) 
        {
            $endLocation = $memLocation + $memoryslots[$j];
            fwrite($file, $memLocation . " " . $endLocation . "\n");
            $memLocation = $endLocation;
        }
    
        // # of processes
        fwrite($file, $pCount . "\n");
    
        // (ID of process) (size of process)
    
        $id = 1;
    
        for ($i = 0; $i < $pCount; $i++) 
        {
            fwrite($file, $id . " ". $processes[$i] . "\n");
            $id++;
        }
    
        fclose($file);
    
        // -----------------------------------------------
    
        // Set flag file to 1 (we are done writing)
    
        $flag = fopen("../../../files/core-e/c-memory/flag-file.txt", "w");
        fwrite($flag, "1");
        fclose($flag);
    }
    
    // Check if form is submitted and handle the logic
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['compare'])) {
        // Convert the processes input to an array of integers
        $processes = isset($_POST['processes']) ? array_map('intval', explode(',', $_POST['processes'])) : [];

        // Convert the memory slots input to an array of integers
        $memoryslots = isset($_POST['memoryslots']) ? array_map('intval', explode(',', $_POST['memoryslots'])) : [];

        $mechanisms =['011', '012', '013'];

        // Write input to input file
        writeInput($processes, $memoryslots);

        // Call java code to write to output

        // (NOTE: The visualization does NOT work if there are more than 4 memory slots in the input. Unknown reason)

        foreach ($mechanisms as $mid) {
            $path = realpath("../../../files/core-e/m-$mid");
            $outputFile = "$path/out-$mid.dat"; // Output file specific to mechanism
            
            // Copy input file to mechanism-specific directory
            $javaInputFile = "$path/in-$mid.dat";
            if (!copy($inputFile, $javaInputFile)) {
                error_log("Failed to copy $inputFile to $javaInputFile");
            } else {
                error_log("Copied $inputFile to $javaInputFile");
            }
    
            // Run Java with the mechanism-specific path
            $javaCommand = "java -classpath " . escapeshellarg(realpath("../../../cgi-bin/core-e/m-$mid")) . " m$mid " . escapeshellarg($path);
            echo "<script>console.log('/var/www/p/f25-01/html/cgi-bin/core-e/m-$mid m$mid');</script>";
            $javaOutput = shell_exec("$javaCommand 2>&1");
            echo "<script>console.log('Java output for m$mid:', " . json_encode($javaOutput) . ");</script>";
            if ($javaOutput) {
                error_log("Java execution output/error for m$mid: $javaOutput");
            } else {
                error_log("Java executed successfully for m$mid (no output)");
            }
    
            // Check if Java wrote the output
            if (!file_exists($outputFile)) {
                error_log("Java failed to create $outputFile for m$mid");
                $defaultOutput = array_fill(0, 32, 0);
                file_put_contents($outputFile, implode(',', $defaultOutput));
                error_log("Wrote fallback output to $outputFile: " . implode(',', $defaultOutput));
            } else {
                error_log("Java successfully wrote to $outputFile for m$mid");
            }
    
            // Optionally store the output for later use
            $results["m$mid"] = file_get_contents($outputFile);
        }

        // Define your page replacement algorithms
        $algorithms = [
            'FF' => 'firstFitAlgorithm',
            'BF' => 'bestFitAlgorithm',
            'WF' => 'worstFitAlgorithm',
            //'MFU' => 'mfuAlgorithm',
            //'LFU' => 'lfuAlgorithm'
        ];

        // Defines the links to the individual testing pages for each algorithm
        $links = [
            'FF' => '../../core-e/m-011',
            'BF' => '../../core-e/m-012',
            'WF' => '../../core-e/m-013'
        ];

        // Run the algorithms on the pages array
        $results = [];
        foreach ($algorithms as $name => $function) {
            // Call the function dynamically based on algorithm name
            $results[$name] = $function($processes, $memoryslots);
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
                        <h3 class="text-center">Memory Allocation Results</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Algorithm</th>
                                    <th>Number of Unallocated Processes</th>
                                    <th>Process Input</th>
                                    <th>Memory Slot Input</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (!empty($results)): ?>
                                    <?php foreach ($results as $algorithm => $faults): ?>
                                        <tr>
                                            <!-- href="#/" -->
                                            <td><a href="<?php echo $links[$algorithm]?>" target="_blank"><?php echo htmlspecialchars($algorithm); ?></a></td>
                                            <td><?php echo htmlspecialchars($faults); ?></td>
                                            <td><?php echo htmlspecialchars(implode(', ', $processes)); ?></td>
                                            <td><?php echo htmlspecialchars(implode(', ', $memoryslots)); ?></td>
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
    
    // Function to implement FF (First Fit) memory allocation algorithm
    function firstFitAlgorithm($processes, $memoryslots) {
        $unallocated = 0;

        // For each process, loop through memory slots available
        foreach ($processes as $process) {
            $allocated = false;

            // Try to allocate to the first fitting memory slot
            for ($i = 0; $i < count($memoryslots); $i++) {
                if ($process <= $memoryslots[$i]) {
                    $memoryslots[$i] -= $process; // Reduce available memory
                    $allocated = true;
                    break; // Stop looking once allocated
                }
            }

            // If we weren't able to allocate the process, increment unallocated counter
            if (!$allocated) {
                $unallocated++;
            }
        }

        // Return number of unallocated processes
        return $unallocated;
    }

    // Function to implement BF (Best Fit) memory allocation algorithm
    function bestFitAlgorithm($processes, $memoryslots) {
        $unallocated = 0;

        // For each process, loop through memory slots available
        foreach ($processes as $process) {
            $smallestindex = -1;

            // Try to allocate to the smallest fitting memory slot
            for ($i = 0; $i < count($memoryslots); $i++) {
                if ($memoryslots[$i] >= $process) {
                    if ($smallestindex == -1 || $memoryslots[$i] < $memoryslots[$smallestindex]) {
                        $smallestindex = $i;
                    }
                }
            }

            if ($smallestindex != -1) {
                $memoryslots[$smallestindex] -= $process;
            }
            else {
                $unallocated++;
            }
        }

        // Return number of unallocated processes
        return $unallocated;
    }

    // Function to implement WF (Worst Fit) memory allocation algorithm
    function worstFitAlgorithm($processes, $memoryslots) {
        $unallocated = 0;

        // For each process, loop through memory slots available
        foreach ($processes as $process) {
            $largestindex = -1;

            // Try to allocate to the largest fitting memory slot
            for ($i = 0; $i < count($memoryslots); $i++) {
                if ($memoryslots[$i] >= $process) {
                    if ($largestindex == -1 || $memoryslots[$i] > $memoryslots[$largestindex]) {
                        $largestindex = $i;
                    }
                }
            }

            if ($largestindex != -1) {
                $memoryslots[$largestindex] -= $process;
            }
            else {
                $unallocated++;
            }
        }

        // Return number of unallocated processes
        return $unallocated;
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