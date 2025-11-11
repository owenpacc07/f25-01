<?php
/**
 * This file creates a new submission folder immediately when user clicks "Make a Submission"
 * It then redirects the user to select a mechanism to work on
 */

require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: ./login.php');
    exit();
}

$user = $_SESSION['userid'];

// If mechanism is specified, create submission and redirect to edit page
if (isset($_GET['mechanism'])) {
    $mechanism_code = $_GET['mechanism'];
    
    // Get mechanism_id from database
    $mechanism_query = "SELECT mechanism_id FROM mechanisms WHERE client_code = ?";
    $stmt = mysqli_prepare($link, $mechanism_query);
    mysqli_stmt_bind_param($stmt, "s", $mechanism_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $mechanism_row = mysqli_fetch_assoc($result);
    
    if (!$mechanism_row) {
        $_SESSION['error_message'] = "Invalid mechanism selected.";
        header('Location: ./create_submission.php');
        exit();
    }
    
    $mechanism = $mechanism_row['mechanism_id'];
    
    // Define file paths
    $inputFilePath = realpath("./files/core-s/m-$mechanism_code/in-$mechanism_code.dat");
    $outputFilePath = realpath("./files/core-s/m-$mechanism_code/out-$mechanism_code.dat");
    $codeFilePath = realpath("./cgi-bin/core-s/m-$mechanism_code/");
    $restrict_view = 1;
    
    // Create submission record in database
    $submission_insert = "INSERT INTO submissions (mechanism_id, user_id, input_path, output_path, code_path, restrict_view) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $submission_query);
    mysqli_stmt_bind_param($stmt, "iisssi", $mechanism, $user, $inputFilePath, $outputFilePath, $codeFilePath, $restrict_view);
    
    if (mysqli_stmt_execute($stmt)) {
        // Get the new submission ID
        $submission_id = mysqli_insert_id($link);
        
        // Create submission folder
        $submission_path = "./files/submissions/" . $submission_id . "_" . $mechanism . "_" . $user;
        
        if (!is_dir($submission_path)) {
            mkdir($submission_path, 0770, true);
            @chown($submission_path, 'nobody');
        }
        
        // Copy default input and output files to submission folder
        if (file_exists($inputFilePath)) {
            copy($inputFilePath, "$submission_path/in-$mechanism_code.dat");
        }
        if (file_exists($outputFilePath)) {
            copy($outputFilePath, "$submission_path/out-$mechanism_code.dat");
        }
        
        // Update submission with correct paths
        $newInputPath = "$submission_path/in-$mechanism_code.dat";
        $newOutputPath = "$submission_path/out-$mechanism_code.dat";
        
        $update_query = "UPDATE submissions SET input_path = ?, output_path = ? WHERE submission_id = ?";
        $stmt = mysqli_prepare($link, $update_query);
        mysqli_stmt_bind_param($stmt, "ssi", $newInputPath, $newOutputPath, $submission_id);
        mysqli_stmt_execute($stmt);
        
        // Store submission ID in session for the edit page
        $_SESSION['current_submission_id'] = $submission_id;
        
        // Redirect to mechanism edit page
        header("Location: ./core-s/m-$mechanism_code/edit.php?submission_id=$submission_id");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to create submission: " . mysqli_error($link);
        header('Location: ./create_submission.php');
        exit();
    }
}

// If no mechanism specified, show mechanism selection page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Mechanism - Create Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include './templates/navbar.php'; ?>
    
    <div class="container mt-5">
        <h2 class="text-center mb-4">Select a Mechanism</h2>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <?php
            // Get all mechanisms from database
            $mechanism_query = "SELECT m.client_code, m.algorithm, c.component_name 
                               FROM mechanisms m 
                               JOIN components c ON m.component_id = c.component_id 
                               WHERE m.client_code LIKE '0%'
                               ORDER BY m.client_code";
            $mechanisms = mysqli_query($link, $mechanism_query);
            
            while ($mech = mysqli_fetch_assoc($mechanisms)):
            ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">M-<?php echo htmlspecialchars($mech['client_code']); ?></h5>
                            <p class="card-text">
                                <strong>Component:</strong> <?php echo htmlspecialchars($mech['component_name']); ?><br>
                                <strong>Algorithm:</strong> <?php echo htmlspecialchars($mech['algorithm']); ?>
                            </p>
                            <a href="create_submission.php?mechanism=<?php echo urlencode($mech['client_code']); ?>" 
                               class="btn btn-primary">Create Submission</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
