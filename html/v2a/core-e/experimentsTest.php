<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php'); 
    exit();
}

// Include database configuration
require '../config.php'; 

// Log messages for debugging
if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

// Handle experiment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitExperiment'])) {
    $user_id = mysqli_real_escape_string($link, $_SESSION['userid']);
    $family_id = mysqli_real_escape_string($link, $_POST['family_id'] ?? '1'); // Default to 1 (CPU scheduling)
    $experiments_data = mysqli_query($link, "SELECT * FROM experiments WHERE user_id = '$user_id'");
    if (!$experiments_data) {
        $_SESSION['log_messages'][] = "Error fetching experiments: " . mysqli_error($link);
    }

    // Fetch mechanisms and algorithm name from comparisons table
    $mechanism_query = "SELECT client_code, algorithm FROM comparisons WHERE component_id = ? LIMIT 1";
    $stmt = $link->prepare($mechanism_query);
    $stmt->bind_param("i", $family_id);
    $stmt->execute();
    $mechanism_result = $stmt->get_result();
    $mechanisms = [];
    $algorithm_name = null;
    while ($row = $mechanism_result->fetch_assoc()) {
        $mechanisms[] = $row['client_code'];
        $algorithm_name = $row['algorithm']; // Get the algorithm name (e.g., "CPU Scheduling")
    }
    if (empty($mechanisms)) {
        $mechanisms = ['001']; // Fallback for CPU scheduling
        $algorithm_name = 'cpu'; // Fallback algorithm name
        $_SESSION['log_messages'][] = "No mechanisms found for family_id $family_id, using fallback.";
    } else {
        $_SESSION['log_messages'][] = "Mechanisms for family_id $family_id: " . implode(', ', $mechanisms);
        $_SESSION['log_messages'][] = "Algorithm name for family_id $family_id: $algorithm_name";
    }

    // Extract the first word of the algorithm name
    $algorithm_first_word = strtolower(explode(' ', trim($algorithm_name))[0]); // Get first word, convert to lowercase

    // Insert one record to get the experiment_id
    $insert_query = "INSERT INTO experiments (family_id, user_id, input_path, output_path, code_path, restrict_view) 
                     VALUES (?, ?, '', '', '', ?)";
    $stmt = $link->prepare($insert_query);
    $restrict_view = 1;
    $stmt->bind_param("iii", $family_id, $user_id, $restrict_view);
    if ($stmt->execute()) {
        $experiment_id = $link->insert_id; // Get the auto-incremented experiment_id
        $_SESSION['log_messages'][] = "Experiment ID created: $experiment_id";
    } else {
        $_SESSION['log_messages'][] = "Error creating experiment: " . $stmt->error;
        header("Location: experiment.php");
        exit();
    }

    // Construct folder name using the database experiment_id
    $base_folder_name = "{$user_id}_{$experiment_id}_{$family_id}";
    $base_path = "/var/www/p/s25-01/html/files/experiments/{$base_folder_name}";

    // Create base experiment folder
    if (!file_exists($base_path)) {
        if (mkdir($base_path, 0777, true)) {
            $_SESSION['log_messages'][] = "Created base directory: $base_path";
        } else {
            $_SESSION['log_messages'][] = "Failed to create base directory: $base_path - " . error_get_last()['message'];
        }
    }

    // Process each mechanism to set the code_path and directory paths
    $first_mechanism = true;
    foreach ($mechanisms as $mechanism_id) {
        // Define code path
        $code_path = "/var/www/p/s25-01/html/cgi-bin/core-e/m-{$mechanism_id}";

        // Update the record with the directory paths and code_path for the first mechanism
        if ($first_mechanism) {
            $input_path_db = "experiments/{$base_folder_name}/";
            $output_path_db = "experiments/{$base_folder_name}/";

            $update_query = "UPDATE experiments SET input_path = ?, output_path = ?, code_path = ? WHERE experiment_id = ?";
            $stmt = $link->prepare($update_query);
            $stmt->bind_param("sssi", $input_path_db, $output_path_db, $code_path, $experiment_id);
            if ($stmt->execute()) {
                $_SESSION['log_messages'][] = "Updated experiment ID $experiment_id with directory paths and code_path for mechanism $mechanism_id";
            } else {
                $_SESSION['log_messages'][] = "Error updating experiment: " . $stmt->error;
            }
            $first_mechanism = false;
        }
    }

    // Redirect to c-<algorithm_first_word>/submissions.php with experiment ID
    header("Location: c-{$algorithm_first_word}/submissions.php?experiment_id=$base_folder_name");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit an Experiment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .container { margin-top: 50px; }
        .btn-primary { background-color: #007bff; border-color: #007bff; }
        .form-control, .form-select { padding: 10px; font-size: 1em; border-radius: 6px; border: 1px solid #ccc; width: 100%; transition: all 0.3s ease; }
        .form-control:focus, .form-select:focus { border-color: #007bff; box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); }
    </style>
</head>
<body>
    <?php include realpath('../navbar.php'); ?>
    <main>
        <div class="container">
            <h2>Submit a New Experiment</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="family_id">Category ID:</label>
                    <select class="form-select" id="family_id" name="family_id">
                        <option value="1" selected>1 - CPU Scheduling</option>
                        <option value="2">2 - Memory Allocation</option>
                        <option value="3">3 - Page Replacement</option>
                        <option value="4">4 - File Allocation</option>
                        <option value="5">5 - Disk Scheduling</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="submitExperiment">Submit Experiment</button>
            </form>
        </div>
    </main>

    <?php
    if (!empty($_SESSION['log_messages'])) {
        foreach ($_SESSION['log_messages'] as $logMessage) {
            echo "<script>console.log('" . addslashes($logMessage) . "');</script>";
        }
        unset($_SESSION['log_messages']);
    }
    ?>
</body>
</html>

<style>
    .custom-btn { display: inline-block; padding: 15px 100px; background: linear-gradient(45deg, #007bff, #00c6ff); color: white; font-size: 25px; font-weight: bold; text-align: center; border-radius: 8px; text-decoration: none; transition: all 0.3s ease; }
    .custom-btn:hover { background: linear-gradient(45deg, #007bff, #00c6ff); transform: scale(1.05); color: white; }
    .container { background-color: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); padding: 30px; margin-bottom: 30px; margin-top: 20px; background: linear-gradient(135deg, #f3f4f6, #ffffff); }
    body { background-color: #f4f7fa; margin: 0; padding: 0; }
    button.btn-danger { border-radius: 5px; transition: background-color 0.3s ease; border: none; }
    button.btn-danger:hover { background-color: #97233f; }
    .form-group label { font-size: 1.1em; font-weight: 500; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px 15px; text-align: left; border: 1px solid #ddd; transition: background-color 0.3s ease; }
    th { background-color: #007bff; color: white; font-size: 1.1em; }
    button.btn-info { background-color: #17a2b8; color: white; border-radius: 5px; }
    td { font-size: 1.1em; }
    .modal-body { font-size: 20px; padding: 20px; overflow: auto; max-height: 400px; }
    .modal-header { background-color: #007bff; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px; }
    .modal-body h5 { font-size: 1.1em; color: #007bff; }
    .modal-body pre { white-space: pre-wrap; }
    .modal-body pre::before { content: "---------------------------------------------------------"; white-space: pre; }
    .modal-body pre::after { content: "---------------------------------------------------------"; white-space: pre; }
</style>