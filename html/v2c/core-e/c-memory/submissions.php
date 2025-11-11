<?php
// Start the session to track the user
session_start();
if (!isset($_SESSION['userid'])) {
    die("User not logged in.");
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
require '../../config.php'; // Adjust the path as needed

// Initialize log messages for debugging
if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

// Get experiment_id from the URL (e.g., submission.php?experiment_id=2_345_1)
$experiment_id_param = $_GET['experiment_id'] ?? null;
if (!$experiment_id_param) {
    $_SESSION['log_messages'][] = "No experiment ID provided.";
    header("Location: ../../index.php");
    exit();
}

// Parse experiment_id into user_id, experiment_db_id, and mechanism_num
$parts = explode('_', $experiment_id_param);
if (count($parts) !== 3) {
    $_SESSION['log_messages'][] = "Invalid experiment ID format.";
    header("Location: ../../index.php");
    exit();
}
$user_id = $parts[0];
$experiment_db_id = $parts[1];
$mechanism_num = $parts[2];

// Sanitize inputs to prevent SQL injection
$user_id = mysqli_real_escape_string($link, $user_id);
$experiment_db_id = mysqli_real_escape_string($link, $experiment_db_id);
$mechanism_num = mysqli_real_escape_string($link, $mechanism_num);

// Fetch family_id from the experiments table for validation
$query = "SELECT family_id FROM experiments WHERE experiment_id = ? AND user_id = ?";
$stmt = $link->prepare($query);
$stmt->bind_param("is", $experiment_db_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $_SESSION['log_messages'][] = "Experiment not found for ID $experiment_db_id and user $user_id";
    header("Location: ../../index.php");
    exit();
}
$row = $result->fetch_assoc();
$family_id = $row['family_id'];

// Define base directory and experiment folder
$base_dir = "/var/www/p/f25-01/html/files/experiments/";
$folder_name = "{$user_id}_{$experiment_db_id}_{$mechanism_num}";
$experiment_path = $base_dir . $folder_name . "/";

// Create the experiment folder if it doesn’t exist
if (!file_exists($experiment_path)) {
    if (!mkdir($experiment_path, 0770, true)) {
        $_SESSION['log_messages'][] = "Failed to create directory: $experiment_path";
        header("Location: ../../index.php");
        exit();
    }
    $_SESSION['log_messages'][] = "Created directory: $experiment_path";
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
        let chart;
        let simulationResults; // Placeholder for results from PHP

        // Create an array to store the colors
        const color = [];
        const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'); // Random number between 0xFFFFFF
        color.push(randomColor);  // Store the random color

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
                        <h3 class="text-center">Memory Allocation Experiment
                            <?php echo htmlspecialchars($experiment_id_param) ?>
                        </h3>
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
                                    value="<?php echo isset($_POST['memoryslots']) ? htmlspecialchars($_POST['memoryslots']) : (isset($_GET['input']) ? htmlspecialchars($_GET['input']) : '100, 500, 200, 300, 600'); ?>"
                                    required>
                            </div>
                            <button type="submit" name="submitInputs" value="Submit"
                                class="btn btn-purple d-inline mr-2">Simulate for
                                Results</button>
                            <button type="button" class="btn btn-purple d-inline mr-2"
                                onclick="randomizeInputs();">Randomize</button>
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
    function writeInput($processes, $memoryslots, $input_file)
    {
        // -- Write input to respective input dat file --
    
        $pCount = count($processes);
        $mCount = count($memoryslots);



        $file = fopen($input_file, "w");

        // # of free memory slots
        fwrite($file, $mCount . "\n");

        // addresses of start and end of a free memory slot
    
        $memLocation = 0;

        for ($j = 0; $j < $mCount; $j++) {
            $endLocation = $memLocation + $memoryslots[$j];
            fwrite($file, $memLocation . " " . $endLocation . "\n");
            $memLocation = $endLocation;
        }

        // # of processes
        fwrite($file, $pCount . "\n");

        // (ID of process) (size of process)
    
        $id = 1;

        for ($i = 0; $i < $pCount; $i++) {
            fwrite($file, $id . " " . $processes[$i] . "\n");
            $id++;
        }

        fclose($file);

        // -----------------------------------------------
    
        // Set flag file to 1 (we are done writing)
    
        /*$flag = fopen("../../../files/core-e/c-memory/flag-file.txt", "w");
        fwrite($flag, "1");
        fclose($flag);*/
    }

    // Check if form is submitted and handle the logic
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitInputs'])) {
        // Convert the processes input to an array of integers
        $processes = isset($_POST['processes']) ? array_map('intval', explode(',', $_POST['processes'])) : [];

        // Convert the memory slots input to an array of integers
        $memoryslots = isset($_POST['memoryslots']) ? array_map('intval', explode(',', $_POST['memoryslots'])) : [];

        // Define the input file path
        $input_file = $experiment_path . "in-memory.dat";

        $input_content_p = trim($_POST['processes'] ?? '');
        $input_content_m = trim($_POST['memoryslots'] ?? '');

        if (empty($input_content_p) || empty($input_content_m)) {
            $_SESSION['error_message'] = "No input data provided.";
        } else {
            // Write the input data to in-memory.dat
    
            if (writeInput($processes, $memoryslots, $input_file) !== false) {
                $_SESSION['log_messages'][] = "Input file saved to $input_file";

                // Define mechanisms for Memory Allocation (e.g., '011' = First Fit, '012' = Best Fit, etc.)
                $mechanisms = ['011', '012', '013'];

                // Run Java programs for each mechanism
                foreach ($mechanisms as $mid) {
                    //$java_command = "java -classpath " . escapeshellarg(realpath("/var/www/p/f25-01/html/cgi-bin/core-c/m-$mid")) . " m$mid " . escapeshellarg($experiment_path);
                    $javaCommand = "java -classpath " . escapeshellarg(realpath("../../../cgi-bin/core-e/m-$mid")) . " m$mid " . escapeshellarg($experiment_path);
                    $java_output = shell_exec("$javaCommand 2>&1");
                    if ($java_output) {
                        $_SESSION['log_messages'][] = "Java output/error for m$mid: $java_output";
                    } else {
                        $_SESSION['log_messages'][] = "Java executed successfully for m$mid (no output)";
                    }

                    // Check if output file was created
                    $output_file = "$experiment_path/out-$mid.dat";
                    if (!file_exists($output_file)) {
                        $_SESSION['log_messages'][] = "Java failed to create $output_file for m$mid";
                        $default_output = "No output generated for m$mid";
                        file_put_contents($output_file, $default_output);
                        $_SESSION['log_messages'][] = "Wrote default output to $output_file";
                    } else {
                        $_SESSION['log_messages'][] = "Java successfully wrote to $output_file for m$mid";
                    }
                }

                $_SESSION['success_message'] = "Input submitted and simulations run successfully!";
            } else {
                $_SESSION['log_messages'][] = "Failed to save input file to $input_file";
                $_SESSION['error_message'] = "Failed to save input file.";
            }
        }
        header("Location: submissions.php?experiment_id=$experiment_id_param");
        exit();
    }
    ?>
    </div>
    </div>


    <?php ?>

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