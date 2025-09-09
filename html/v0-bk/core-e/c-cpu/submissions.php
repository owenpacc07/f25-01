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
$base_dir = "/var/www/p/s25-01/html/files/experiments/";
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

// Define the input file path
$input_file = $experiment_path . "in-cpu.dat";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitInputs'])) {
    $input_content = trim($_POST['processes'] ?? '');
    if (empty($input_content)) {
        $_SESSION['error_message'] = "No input data provided.";
    } else {
        // Write the input data to in-cpu.dat
        if (file_put_contents($input_file, $input_content) !== false) {
            $_SESSION['log_messages'][] = "Input file saved to $input_file";

            // Define mechanisms for CPU scheduling (e.g., '001' = FCFS, '002' = SJF, etc.)
            $mechanisms = ['001', '002', '003', '004', '005', '006', '007', '008'];

            // Run Java programs for each mechanism
            foreach ($mechanisms as $mid) {
                $java_command = "java -classpath " . escapeshellarg(realpath("/var/www/p/s25-01/html/cgi-bin/core-c/m-$mid")) . " m$mid " . escapeshellarg($experiment_path);
                $java_output = shell_exec("$java_command 2>&1");
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

// Load existing input data (if any) to display in the textarea
$input = file_exists($input_file) ? file_get_contents($input_file) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Input for Experiment</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Generate random input for testing
        function generateRandomInput() {
            const numProcesses = 4;
            let randomProcesses = [];
            for (let i = 1; i <= numProcesses; i++) {
                const processId = `${i}`;
                const arrivalTime = Math.floor(Math.random() * 10);
                const burstTime = Math.floor(Math.random() * 10) + 1;
                const priority = Math.floor(Math.random() * 5) + 1;
                randomProcesses.push(`${processId}, ${arrivalTime}, ${burstTime}, ${priority}`);
            }
            document.getElementById('processInput').value = randomProcesses.join('\n');
        }
    </script>
</head>
<body>
    <?php include realpath('../../navbar.php'); ?>

    <div class="container mt-3">
        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Submit Input for Experiment <?php echo htmlspecialchars($experiment_id_param); ?></h3>
                    </div>
                    <div class="card-body">
                        <p class="text-center">Input Data:</p>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="processInput">Enter Process Details (Format: Process ID, Arrival Time, Burst Time, Priority):</label>
                                <textarea name="processes" class="form-control" id="processInput" rows="5" required><?php echo htmlspecialchars($input); ?></textarea>
                            </div>
                            <button type="button" class="btn btn-purple mr-2" onclick="generateRandomInput()">Generate Random Input</button>
                            <button type="submit" name="submitInputs" class="btn btn-purple">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Log messages for debugging (visible in browser console)
    if (!empty($_SESSION['log_messages'])) {
        foreach ($_SESSION['log_messages'] as $logMessage) {
            echo "<script>console.log('" . addslashes($logMessage) . "');</script>";
        }
        unset($_SESSION['log_messages']);
    }
    ?>

    <style>
        .btn-purple {
            background-color: #9769D9;
            color: white;
            border-radius: 8px;
        }
        .btn-purple:hover {
            background-color: #B594E4;
            color: white;
        }
        .card-header {
            background-color: #9769D9;
            color: white;
            font-weight: bold;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</body>
</html>