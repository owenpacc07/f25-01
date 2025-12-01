<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// Include database configuration
require '../config.php';


// This ModeID check is currently not restricting any users anymore, but is kept for redundancy.
// Check if the logged-in user has ModeID >= 0 from sql tables
// this means research user,manage user or admin.
$email = mysqli_real_escape_string($link, $_SESSION['email']);
$mode_query = "SELECT ModeID FROM users WHERE Email = ?";
$stmt = $link->prepare($mode_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$mode_result = $stmt->get_result();
$mode_row = $mode_result->fetch_assoc();

if (!$mode_row || $mode_row['ModeID'] < 0) {
    $_SESSION['log_messages'][] = "Access denied: Invalid user account.";
    header('Location: ../login.php'); // Redirect to an access-denied page
    exit();
}

// Log messages for debugging
if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

// Fetch experiments data for the logged-in user
$user_id = mysqli_real_escape_string($link, $_SESSION['userid']);
$experiments_data = mysqli_query($link, "SELECT e.experiment_id, e.family_id, e.user_id, e.input_path, e.output_path, e.code_path, e.created_at FROM experiments e WHERE e.user_id = '$user_id' ORDER BY e.experiment_id DESC");
if (!$experiments_data) {
    $_SESSION['log_messages'][] = "Error fetching experiments: " . mysqli_error($link);
} else {
    $_SESSION['log_messages'][] = "Found " . mysqli_num_rows($experiments_data) . " experiments for user_id: $user_id";
}

// Handle experiment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitExperiment'])) {
    $family_id = mysqli_real_escape_string($link, $_POST['family_id'] ?? '1');

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
        $algorithm_name = $row['algorithm'];
    }
    if (empty($mechanisms)) {
        $mechanisms = ['001'];
        $algorithm_name = 'cpu';
        $_SESSION['log_messages'][] = "No mechanisms found for family_id $family_id in comparisons, using fallback.";
    } else {
        $_SESSION['log_messages'][] = "Mechanisms for family_id $family_id: " . implode(', ', $mechanisms);
        $_SESSION['log_messages'][] = "Algorithm name for family_id $family_id: $algorithm_name";
    }

    // Extract the first word of the algorithm name
    $algorithm_first_word = strtolower(explode(' ', trim($algorithm_name))[0]);

    // Insert one record to get the experiment_id
    $insert_query = "INSERT INTO experiments (family_id, user_id, input_path, output_path, code_path, restrict_view) 
                     VALUES (?, ?, '', '', '', ?)";
    $stmt = $link->prepare($insert_query);
    $restrict_view = 1;
    $stmt->bind_param("iii", $family_id, $user_id, $restrict_view);
    if ($stmt->execute()) {
        $experiment_id = $link->insert_id;
        $_SESSION['log_messages'][] = "Experiment ID created: $experiment_id";
    } else {
        $_SESSION['log_messages'][] = "Error creating experiment: " . $stmt->error;
        header("Location: experiment.php");
        exit();
    }

    // Construct folder name using the database experiment_id
    $base_folder_name = "{$user_id}_{$experiment_id}_{$family_id}";
    $base_path = "/var/www/p/f25-01/html/files/experiments/{$base_folder_name}";

    // Create base experiment folder
    if (!file_exists($base_path)) {
        if (mkdir($base_path, 0777, true)) {
            $_SESSION['log_messages'][] = "Created base directory: $base_path";
        } else {
            $_SESSION['log_messages'][] = "Failed to create base directory: $base_path - " . error_get_last()['message'];
        }
    }

    // Process each mechanism to set the directory paths
    $first_mechanism = true;
    foreach ($mechanisms as $mechanism_id) {
        $code_path = "/var/www/p/f25-01/html/cgi-bin/core-e/m-{$mechanism_id}";
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
        .form-control, .form-select { padding: 10px; font-size: 1em; border-radius: 8px; border: 1px solid #ccc; width: 100%; transition: all 0.3s ease; }
        .form-control:focus, .form-select:focus { border-color: #007bff; box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); }
    </style>
</head>
<body>
    <?php include realpath('../navbar.php'); ?>
    <main>
        <!-- Submit a New Experiment Box -->
        <div class="container submission-box">
            <h2>Submit a New Experiment</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="family_id">Category:</label>
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

        <!-- Your Experiments Box -->
        <?php if ($experiments_data && mysqli_num_rows($experiments_data) > 0): ?>
            <div class="container experiments-box">
                <section>
                    <h2>Your Experiments</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Experiment ID</th>
                                <th>Date/Time</th>
                                <th>Family ID</th>
                                <th>User Email</th>
                                <th>Input Path</th>
                                <th>View Mechanisms</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($experiments_data)): 
                                $input_modal_id = "inputModal_" . $row['experiment_id'];
                                $mechanism_modal_id = "mechanismModal_" . $row['experiment_id'];

                                // Get user email from the users table based on user_id (matching core-s pattern)
                                $user_id_row = $row['user_id'];
                                $email_query = "SELECT email FROM users WHERE UserID = '$user_id_row'";
                                $email_result = mysqli_query($link, $email_query);
                                $user_email = ($email_result && mysqli_num_rows($email_result) > 0) ? 
                                              mysqli_fetch_assoc($email_result)['email'] : 'Unknown';

                                // Fetch algorithm name for display
                                $family_id = $row['family_id'];
                                $mechanism_query = "SELECT client_code, algorithm FROM comparisons WHERE component_id = ? LIMIT 1";
                                $stmt = $link->prepare($mechanism_query);
                                $stmt->bind_param("i", $family_id);
                                $stmt->execute();
                                $mechanism_result = $stmt->get_result();
                                $mechanism_row = $mechanism_result->fetch_assoc();
                                $algorithm_name = $mechanism_row['algorithm'] ?? 'Unknown Algorithm';
                                $algorithm_first_word = strtolower(explode(' ', trim($algorithm_name))[0]);

                                // Fetch all mechanisms for this family_id (component_id)
                                $mechanisms_query = "SELECT client_code, algorithm FROM mechanisms WHERE component_id = ?";
                                $stmt = $link->prepare($mechanisms_query);
                                $stmt->bind_param("i", $family_id);
                                $stmt->execute();
                                $mechanisms_result = $stmt->get_result();
                                if (!$mechanisms_result) {
                                    $_SESSION['log_messages'][] = "Error fetching mechanisms for family_id $family_id: " . mysqli_error($link);
                                } else {
                                    $_SESSION['log_messages'][] = "Found " . $mechanisms_result->num_rows . " mechanisms for family_id $family_id";
                                }
                            ?>
                                <tr>
                                    <td><?php echo $row['experiment_id']; ?></td>
                                    <td><?php echo isset($row['created_at']) ? $row['created_at'] : date('Y-m-d H:i:s'); ?></td>
                                    <td><?php echo $algorithm_name . " (ID: " . $row['family_id'] . ")"; ?></td>
                                    <td><?php echo $user_email; ?></td>
                                    <td><button class='btn btn-info' data-toggle='modal' data-target='#<?php echo $input_modal_id ?>'>View Input</button></td>
                                    <td><button class='btn btn-primary' data-toggle='modal' data-target='#<?php echo $mechanism_modal_id ?>'>View Mechanisms</button></td>
                                </tr>

                                <!-- Modal for Input -->
                                <div class="modal fade" id="<?php echo $input_modal_id ?>" tabindex="-1" role="dialog" aria-labelledby="inputModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="inputModalLabel">Input File Content</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">X</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <h5>Input Data</h5>
                                                <pre class="bg-light p-3 rounded">
                                                    <?php
                                                    $experiment_id = $row['experiment_id'];
                                                    $user_id_modal = $row['user_id'];
                                                    $experiment_directory_path = realpath("/var/www/p/f25-01/html/files/experiments");
                                                    $inputPath = sprintf("%s/%d_%d_%d/in-%s.dat", $experiment_directory_path, $user_id_modal, $experiment_id, $family_id, $algorithm_first_word);
                                                    if (file_exists($inputPath)) {
                                                        echo file_get_contents($inputPath);
                                                    } else {
                                                        echo "Input file not found: $inputPath";
                                                    }
                                                    ?>
                                                </pre>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                               <!-- Modal for Mechanisms -->
                                <div class="modal fade" id="<?php echo $mechanism_modal_id ?>" tabindex="-1" role="dialog" aria-labelledby="mechanismModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="mechanismModalLabel">Mechanisms for <?php echo $algorithm_name; ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">X</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <h5>Available Mechanisms</h5>
                                                <?php if ($mechanisms_result->num_rows > 0): ?>
                                                    <ul class="mechanism-list">
                                                        <?php while ($mechanism = $mechanisms_result->fetch_assoc()): ?>
                                                            <li>
                                                                <a href="/p/f25-01/v2/core-e/m-<?php echo $mechanism['client_code']; ?>?user_id=<?php echo $user_id_modal; ?>&experiment_id=<?php echo $user_id_modal . '_' . $row['experiment_id'] . '_' . $family_id; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=<?php echo $mechanism['client_code']; ?>" target="_blank">
                                                                    <?php echo $mechanism['algorithm']; ?> (m-<?php echo $mechanism['client_code']; ?>)
                                                                </a>
                                                            </li>
                                                        <?php endwhile; ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <p>No mechanisms found for this category.</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>
            </div>
        <?php else: ?>
            <div class="container">
                <p>No experiments found. Create your first experiment above!</p>
            </div>
        <?php endif; ?>
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
    .container { 
        background-color: white; 
        border-radius: 8px; 
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); 
        padding: 30px; 
        margin-bottom: 30px; 
        margin-top: 20px; 
        background: linear-gradient(135deg, #f3f4f6, #ffffff); 
    }
    .submission-box { margin-top: 50px; }
    .experiments-box { margin-top: 20px; }
    body { background-color: #f4f7fa; margin: 0; padding: 0; }
    button.btn-danger { border-radius: 8px; transition: background-color 0.3s ease; border: none; }
    button.btn-danger:hover { background-color: #97233f; }
    .form-group label { font-size: 1.1em; font-weight: 500; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px 15px; text-align: left; border: 1px solid #ddd; transition: background-color 0.3s ease; }
    th { background-color: #007bff; color: white; font-size: 1.1em; }
    button.btn-info { background-color: #17a2b8; color: white; border-radius: 8px; }
    button.btn-primary { background-color: #007bff; color: white; border-radius: 8; }
    td { font-size: 1.1em; }
    .modal-body { font-size: 20px; padding: 20px; overflow: auto; max-height: 400px; }
    .modal-header { background-color: #007bff; color: white; border-top-left-radius: 6px; border-top-right-radius: 6px; }
    .modal-body h5 { font-size: 1.1em; color: #007bff; }
    .modal-body pre { white-space: pre-wrap; }
    .modal-body pre::before { content: "---------------------------------------------------------"; white-space: pre; }
    .modal-body pre::after { content: "---------------------------------------------------------"; white-space: pre; }
    .modal-body ul { list-style-type: none; padding-left: 0; }
    .modal-body ul li { margin-bottom: 10px; }
    .modal-body ul li a { color: #007bff; text-decoration: none; }
    .modal-body ul li a:hover { text-decoration: underline; }
</style>