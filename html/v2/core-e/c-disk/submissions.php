<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../../login.php');
    exit();
}

// Include database configuration
require '../../config.php';
require '../../system.php';

// Log messages for debugging
if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

// Ensure experiment_id parameter is provided
if (!isset($_GET['experiment_id'])) {
    $_SESSION['error_message'] = "No experiment ID provided.";
    header("Location: ../index.php");
    exit();
}

$experiment_id_param = $_GET['experiment_id'];
list($user_id, $experiment_id, $family_id) = explode('_', $experiment_id_param);

// Validate extracted values
if (!$user_id || !$experiment_id || !$family_id) {
    $_SESSION['error_message'] = "Invalid experiment ID format.";
    header("Location: ../index.php");
    exit();
}

// Sanitize inputs
$user_id = mysqli_real_escape_string($link, $user_id);
$experiment_id = mysqli_real_escape_string($link, $experiment_id);
$family_id = mysqli_real_escape_string($link, $family_id);

// Define experiment path using the passed experiment_id
$base_path = realpath("../../../files/experiments/");
if ($base_path === false) {
    $_SESSION['log_messages'][] = "Base path resolution failed for ../../../files/experiments/";
    $_SESSION['error_message'] = "Invalid base experiments path.";
    header("Location: ../index.php");
    exit();
}
$experiment_path = "$base_path/$experiment_id_param";

// Check if the experiment folder exists
if (!file_exists($experiment_path)) {
    $_SESSION['log_messages'][] = "Experiment folder does not exist: $experiment_path";
    if (!mkdir($experiment_path, 0777, true)) {
        $_SESSION['error_message'] = "Failed to create experiment folder.";
        header("Location: ../index.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitInput'])) {
    $requests = trim($_POST['requests'] ?? '');
    $head = intval($_POST['head'] ?? 50);
    $diskSize = intval($_POST['disk_size'] ?? 200);
    
    if (empty($requests)) {
        $_SESSION['error_message'] = "No input data provided.";
    } else {
        // Format the input for disk scheduling algorithms
        $request_values = array_map('intval', array_map('trim', explode(',', $requests)));
        $formatted_content = "0 $diskSize\n$head\n" . implode(' ', $request_values);

        // Write input file to experiment folder
        $input_file = "$experiment_path/in-disk.dat";
        if (file_put_contents($input_file, $formatted_content) === false) {
            $_SESSION['log_messages'][] = "Failed to write to $input_file - " . error_get_last()['message'];
            $_SESSION['error_message'] = "Failed to save input file.";
        } else {
            $_SESSION['log_messages'][] = "Wrote to $input_file: $formatted_content";

            // Mechanisms to process
            $mechanisms = ['041', '042', '043', '044', '045'];

            // Run Java programs
            foreach ($mechanisms as $mid) {
                $java_path = realpath("../../../cgi-bin/core-e/m-$mid");
                if ($java_path === false) {
                    $_SESSION['log_messages'][] = "Java path resolution failed for ../../../cgi-bin/core-e/m-$mid";
                    continue;
                }
                $java_command = "java -classpath " . escapeshellarg($java_path) . " m$mid " . escapeshellarg($experiment_path);
                $java_output = shell_exec("$java_command 2>&1");
                if ($java_output) {
                    $_SESSION['log_messages'][] = "Java output/error for m$mid: $java_output";
                } else {
                    $_SESSION['log_messages'][] = "Java executed successfully for m$mid (no output)";
                }

                // Check output file
                $output_file = "$experiment_path/out-$mid.dat";
                if (!file_exists($output_file)) {
                    $_SESSION['log_messages'][] = "Java failed to create $output_file for m$mid";
                    file_put_contents($output_file, "0 $diskSize\n$head\n" . implode(' ', $request_values));
                }
            }
            
            $_SESSION['last_requests'] = $requests;
            $_SESSION['last_head'] = $head;
            $_SESSION['last_disk_size'] = $diskSize;
            $_SESSION['success_message'] = "Input processed successfully for experiment ID: $experiment_id.";
            header("Location: submissions.php?experiment_id=$experiment_id_param");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disk Scheduling Submission</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function generateRandomInput() {
            const diskSize = 200;
            const head = Math.floor(Math.random() * diskSize);
            const requestCount = Math.floor(Math.random() * 6) + 5; // 5-10 requests
            
            let requests = [];
            for (let i = 0; i < requestCount; i++) {
                requests.push(Math.floor(Math.random() * diskSize));
            }
            
            document.getElementById('headInput').value = head;
            document.getElementById('diskSizeInput').value = diskSize;
            document.getElementById('requestsInput').value = requests.join(', ');
        }
    </script>
</head>
<body>
    <?php include realpath('../../navbar.php'); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center">Disk Scheduling Experiment</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label for="requestsInput">Request Queue (comma-separated):</label>
                                <input type="text" name="requests" class="form-control" id="requestsInput"
                                    placeholder="e.g., 98, 183, 37, 122, 14, 124, 65, 67"
                                    value="<?php echo isset($_POST['requests']) ? htmlspecialchars($_POST['requests']) : '98, 183, 37, 122, 14, 124, 65, 67'; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="headInput">Head Position:</label>
                                <input type="number" name="head" class="form-control" id="headInput"
                                    min="0" max="999"
                                    value="<?php echo isset($_POST['head']) ? intval($_POST['head']) : 50; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="diskSizeInput">Disk Size:</label>
                                <input type="number" name="disk_size" class="form-control" id="diskSizeInput"
                                    min="100" max="1000"
                                    value="<?php echo isset($_POST['disk_size']) ? intval($_POST['disk_size']) : 200; ?>"
                                    required>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="submitInput" class="btn btn-primary mr-2">Submit Input</button>
                                <button type="button" class="btn btn-secondary" onclick="generateRandomInput()">Generate Random Input</button>
                            </div>
                        </form>

                        <?php if (isset($_SESSION['last_requests'])): ?>
                            <div class="mt-4 text-center">
                                <h4>View Mechanisms</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <a href="/p/f25-01/v2/core-e/m-041?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=041" target="_blank" class="btn btn-info btn-block">FCFS</a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="/p/f25-01/v2/core-e/m-042?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=042" target="_blank" class="btn btn-info btn-block">SSTF</a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="/p/f25-01/v2/core-e/m-043?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=043" target="_blank" class="btn btn-info btn-block">SCAN</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <a href="/p/f25-01/v2/core-e/m-044?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=044" target="_blank" class="btn btn-info btn-block">C-SCAN</a>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <a href="/p/f25-01/v2/core-e/m-045?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=045" target="_blank" class="btn btn-info btn-block">LOOK</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (!empty($_SESSION['log_messages'])) {
        foreach ($_SESSION['log_messages'] as $logMessage) {
            echo "<script>console.log('" . addslashes($logMessage) . "');</script>";
        }
        unset($_SESSION['log_messages']);
    }
    ?>
</body>
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        border-radius: 10px 10px 0 0;
    }
    .btn-primary {
        background-color: #007bff;
    }
    .btn-secondary {
        background-color: #6c757d;
    }
    .btn-info {
        background-color: #17a2b8;
    }
    .btn {
        border-radius: 5px;
    }
</style>
</html>
