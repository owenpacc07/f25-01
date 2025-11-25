<?php
session_start();
// For console logging messages
if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

// If no user is logged in they can't access advanced mode
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// establish db connection
require '../config.php';

// Get user ID from session BEFORE the make_submission handler
$user = $_SESSION['userid'] ?? null;

if (!$user) {
    header('Location: ../login.php');
    exit();
}

if (isset($_SESSION['user_type'])) {
    $_SESSION['log_messages'][] = "User Type in session: " . $_SESSION['user_type'];
}

// Determine if the logged-in user is an admin based on userType
$isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 1;
$_SESSION['log_messages'][] = "User Role: " . ($isAdmin ? 'Admin' : 'User');

// Initialize the submissions data to null
$submissions_data = null;
$mechanism_id = null;

// Check if the 'mechanism_id' is set via GET or POST
if (isset($_POST['mechanism_id']) || isset($_GET['mechanism_id'])) {
    $mechanism_id = mysqli_real_escape_string($link, $_POST['mechanism_id'] ?? $_GET['mechanism_id']);
    $_SESSION['log_messages'][] = "Selected Mechanism ID: " . $mechanism_id;

    // Query the submissions table based on if admin or not
    if ($isAdmin) {
        $submissions_data = mysqli_query($link, "SELECT * FROM submissions WHERE mechanism_id = '$mechanism_id' ORDER BY submission_id DESC");
    } else {
        $user_id = mysqli_real_escape_string($link, $_SESSION['userid']); // Escape user ID for safety
        $submissions_data = mysqli_query($link, "SELECT * FROM submissions WHERE user_id = '$user_id' AND mechanism_id = '$mechanism_id' ORDER BY submission_id DESC");
    }

}

// If no 'mechanism_id' is specified (or the search field is empty), fetch all submissions for the logged-in user (or all if admin)
if (!$mechanism_id) {
    if ($isAdmin) {
        $submissions_data = mysqli_query($link, "SELECT * FROM submissions ORDER BY submission_id DESC");
    } else {
        $user_id = mysqli_real_escape_string($link, $_SESSION['userid']);
        $submissions_data = mysqli_query($link, "SELECT * FROM submissions WHERE user_id = '$user_id' ORDER BY submission_id DESC");
    }
}


// Logic to delete a submission based on the submission ID
if (isset($_POST['deleteSubmissionID'])) {
    $deleteSubmissionID = $_POST['deleteSubmissionID'];

    // Fetch the folder name based on submission id_mechanism id_user id
    $fetchQuery = "SELECT mechanism_id, user_id FROM submissions WHERE submission_id = '$deleteSubmissionID'";
    $fetchResult = mysqli_query($link, $fetchQuery);

    if ($fetchResult && mysqli_num_rows($fetchResult) > 0) {
        $row = mysqli_fetch_assoc($fetchResult);
        $mechanism_id = $row['mechanism_id'];
        $user_id = $row['user_id'];
        $folderName = "{$deleteSubmissionID}_{$mechanism_id}_{$user_id}";

        // Specify the base directory
        $baseDirectory = '/var/www/projects/f24-02/html/files/submissions/';
        $folderPath = $baseDirectory . $folderName;

        // Check if the folder exists
        if (is_dir($folderPath)) {
            $_SESSION['log_messages'][] = "Folder exists: $folderPath";

            // Get the files in the folder
            $files = array_diff(scandir($folderPath), ['.', '..']);
            $_SESSION['log_messages'][] = "Files in folder: " . json_encode($files);

            // Delete all files inside the folder
            foreach ($files as $file) {
                $filePath = $folderPath . '/' . $file;
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        $_SESSION['log_messages'][] = "Deleted file: $filePath";
                    } else {
                        $_SESSION['log_messages'][] = "Failed to delete file: $filePath";
                    }
                }
            } 

            // Check remaining files before attempting to delete the folder
            $remainingFiles = array_diff(scandir($folderPath), ['.', '..']);
            $_SESSION['log_messages'][] = "Remaining files: " . json_encode($remainingFiles);

            // Delete the empty folder
            if (rmdir($folderPath)) {
                $_SESSION['log_messages'][] = "Deleted folder: $folderPath";
            } else {
                $_SESSION['log_messages'][] = "Failed to delete folder: $folderPath";
            }
        } else {
            $_SESSION['log_messages'][] = "Folder not found: $folderPath";
        }

        // Delete the submission from the database
        $deleteQuery = "DELETE FROM submissions WHERE submission_id = '$deleteSubmissionID'";
        $deleteResult = mysqli_query($link, $deleteQuery);

        if (!$deleteResult) {
            $_SESSION['log_messages'][] = "Error deleting submission from database: " . mysqli_error($link);
        } else {
            // Redirect to the same page without any filtering by 'mechanism_id'
	    header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

// Check if the form has been submitted and the 'algorithmSubmit' button has been clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['algorithmSubmit'])) {
    
    // Get the mechanism code from the POST data
    $mechanism_code = $_POST['algorithm-options'];
    // Create an SQL query to retrieve the mechanism ID based on the mechanism code
    $mechanism_query = "select mechanism_id from mechanisms where mechanisms.client_code = '$mechanism_code';";
    $mechanism_result = mysqli_query($link, $mechanism_query);
    $mechanism = mysqli_fetch_all($mechanism_result)[0][0];

    // Check if the mechanism was found
    if ($mechanism) {
        // Redirect the user to the ../core-s/$mechanism_code/edit.php page
        header("Location: ./m-$mechanism_code/edit.php");
        exit(); // Ensure no further code is executed after the redirect
    } else {
        // Handle the case where the mechanism was not found
        echo "Mechanism not found.";
    }
}

// Handle "Make a Submission" button click
if (isset($_POST['action']) && $_POST['action'] === 'make_submission' && isset($_POST['mid'])) {
    try {
        $mid = (int)$_POST['mid'];
        $mid_padded = str_pad($mid, 3, '0', STR_PAD_LEFT);
        $session_key = "submission_m{$mid_padded}_uid{$user}";
        
        // Log to console
        $_SESSION['log_messages'][] = "DEBUG: Creating NEW submission - MID: $mid, MID_PADDED: $mid_padded, USER: $user, SESSION_KEY: $session_key";
        
        // Get mechanism_id
        $mech_query = "SELECT mechanism_id FROM mechanisms WHERE client_code = ?";
        $stmt = mysqli_prepare($link, $mech_query);
        mysqli_stmt_bind_param($stmt, "i", $mid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $mech_row = mysqli_fetch_assoc($result);
        
        if (!$mech_row) {
            throw new Exception("Mechanism not found");
        }
        
        $mechanism_id = $mech_row['mechanism_id'];
        $mid_padded = str_pad($mid, 3, '0', STR_PAD_LEFT);
        
        // Use realpath for existing files, construct proper paths for submission files
        $basePath = realpath(__DIR__ . "/../files/core-s/m-{$mid_padded}");
        if (!$basePath) {
            // Try alternative path
            $basePath = realpath(__DIR__ . "/../../files/core-s/m-{$mid_padded}");
        }
        if (!$basePath) {
            $_SESSION['log_messages'][] = "DEBUG: Path attempts failed - __DIR__: " . __DIR__;
            $_SESSION['log_messages'][] = "DEBUG: Looking for: " . __DIR__ . "/../files/core-s/m-{$mid_padded}";
            throw new Exception("Mechanism directory not found: m-{$mid_padded}");
        }
        
        $_SESSION['log_messages'][] = "DEBUG: Found basePath: $basePath";
        
        // Try to find code file path, but don't fail if it doesn't exist
        $codeFilePath = realpath(__DIR__ . "/../cgi-bin/core-s/m-{$mid_padded}/");
        if (!$codeFilePath) {
            $codeFilePath = realpath(__DIR__ . "/../../cgi-bin/core-s/m-{$mid_padded}/");
        }
        if (!$codeFilePath) {
            // Use a placeholder path if directory doesn't exist
            $codeFilePath = __DIR__ . "/../cgi-bin/core-s/m-{$mid_padded}/";
        }
        
        $_SESSION['log_messages'][] = "DEBUG: codeFilePath: $codeFilePath";
        
        $restrict_view = 1;
        
        // Create submission folder with absolute path FIRST
        $submissions_base = realpath(__DIR__ . "/../files/submissions/");
        if (!$submissions_base) {
            $submissions_base = realpath(__DIR__ . "/../../files/submissions/");
        }
        if (!$submissions_base) {
            throw new Exception("Submissions directory not found");
        }
        
        $_SESSION['log_messages'][] = "DEBUG: submissions_base: $submissions_base";
        
        // We need to create the folder first to get a submission_id, but we need the submission_id to create the folder
        // So we'll use temporary paths and update after folder creation
        $temp_in_path = $submissions_base . "/temp_in.dat";
        $temp_out_path = $submissions_base . "/temp_out.dat";
        
        // Create submission in database with temporary paths
        $query = "INSERT INTO submissions (mechanism_id, user_id, input_path, output_path, code_path, restrict_view) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $query);
        
        if (!$stmt) {
            $_SESSION['log_messages'][] = "DEBUG: Prepare failed: " . mysqli_error($link);
            throw new Exception("Database prepare failed: " . mysqli_error($link));
        }
        
        mysqli_stmt_bind_param($stmt, "iisssi", $mechanism_id, $user, $temp_in_path, $temp_out_path, $codeFilePath, $restrict_view);
        
        if (!mysqli_stmt_execute($stmt)) {
            $_SESSION['log_messages'][] = "DEBUG: Execute failed: " . mysqli_stmt_error($stmt);
            throw new Exception("Database insert failed: " . mysqli_stmt_error($stmt));
        }
        
        $submission_id = mysqli_insert_id($link);
        $_SESSION[$session_key] = $submission_id;
        
        $_SESSION['log_messages'][] = "DEBUG: NEW Submission created - ID: $submission_id, SESSION_KEY: $session_key";
        
        $submission_folder = $submissions_base . "/{$submission_id}_{$mechanism_id}_{$user}/";
        
        $_SESSION['log_messages'][] = "DEBUG: Creating folder at: $submission_folder";
        
        if (!is_dir($submission_folder)) {
            mkdir($submission_folder, 0770, true);
            if (function_exists('chown')) {
                @chown($submission_folder, 'nobody');
            }
        }
        
        // Copy or create files with default test data
        $inputPath = $basePath . "/in-$mid.dat";
        $outputPath = $basePath . "/out-$mid.dat";
        
        $_SESSION['log_messages'][] = "DEBUG: Copying from inputPath: $inputPath (exists: " . (file_exists($inputPath) ? 'YES' : 'NO') . ")";
        $_SESSION['log_messages'][] = "DEBUG: Copying from outputPath: $outputPath (exists: " . (file_exists($outputPath) ? 'YES' : 'NO') . ")";
        
        $copySuccess = true;
        if (file_exists($inputPath)) {
            copy($inputPath, "$submission_folder/in-$mid.dat");
        } else {
            $_SESSION['log_messages'][] = "DEBUG: Input source file does not exist: $inputPath";
            // Create file with sample process data
            $defaultInput = "1 0 7 1\n2 2 4 2\n3 4 1 3";
            file_put_contents("$submission_folder/in-$mid.dat", $defaultInput);
        }
        
        if (file_exists($outputPath)) {
            copy($outputPath, "$submission_folder/out-$mid.dat");
        } else {
            $_SESSION['log_messages'][] = "DEBUG: Output source file does not exist: $outputPath";
            // Create empty output file
            file_put_contents("$submission_folder/out-$mid.dat", "");
        }
        
        $_SESSION['log_messages'][] = "DEBUG: File copy operation " . ($copySuccess ? "completed" : "had issues");
        
        // Update database with actual submission folder paths
        $new_in_path = $submission_folder . "in-$mid.dat";
        $new_out_path = $submission_folder . "out-$mid.dat";
        
        $_SESSION['log_messages'][] = "DEBUG: Saving to DB - in_path: $new_in_path";
        $_SESSION['log_messages'][] = "DEBUG: Saving to DB - out_path: $new_out_path";
        
        $update = "UPDATE submissions SET input_path = ?, output_path = ? WHERE submission_id = ?";
        $stmt = mysqli_prepare($link, $update);
        mysqli_stmt_bind_param($stmt, "ssi", $new_in_path, $new_out_path, $submission_id);
        mysqli_stmt_execute($stmt);

//Save the mechanismID and submissionID to SESSION variables
$_SESSION['mechanismID'] = $mechanism_id;
$_SESSION['submissionID'] = $submission_id;
$_SESSION['submissionFolder'] = $submission_folder;
        
        // Redirect to edit page
        header("Location: ./m-{$mid_padded}/edit.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['log_messages'][] = "DEBUG: Exception during submission creation: " . $e->getMessage();
        die("Error creating submission: " . htmlspecialchars($e->getMessage()));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Make a Submission</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" type="text/css" href="styles/index_styles.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
  
</head>

<body>

<?php include '../navbar.php'; ?>
    <main>
        
        <div class="container">
            <form method="POST" action="">
                <!--Section for a dropdown menu to view the algorithms to select-->
                <div class="form-row align-items-center">
                    <div class="col-auto" style="flex-grow: 1;">
                        <label name="algorithm" for="algorithm">Algorithms:</label>
                        <select class="form-select" id="algorithm-options" name="algorithm-options">
                            <?php
                                $algorithm_query = "select mechanisms.client_code as client_code, components.component_name as component, mechanisms.algorithm as algorithm from components inner join mechanisms on components.component_id = mechanisms.component_id order by client_code;";
                                $algorithm_result = mysqli_query($link, $algorithm_query);
                                while ($row = mysqli_fetch_assoc($algorithm_result)):
                            ?>
                            <option value="<?php echo $row['client_code'] ?>">
                            <?php echo $row['client_code'] . " " . $row['component'] . " " . $row['algorithm'] ?>
                            </option>
                            <?php
                                endwhile;
                            ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="hidden" name="action" value="make_submission">
                        <input type="hidden" name="mid" id="mid-input" value="">
                        <button type="button" class="btn custom-btn" onclick="submitMakeSubmission()" style="margin-left: 0; margin-top: 32px;">Make a Submission</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="container">
            <form method="POST" class="form-inline">
            <div class="form-group mb-2">
                <h2>Search for a submission by Mechanism ID</h2>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <input type="text" class="form-control" id="mechanism_id" name="mechanism_id" placeholder="Enter a Mechanism ID"> 
		    </div>
            <button type="submit" class="btn btn-primary mb-2">Search</button>
            </form>
        </div>
        <?php if ($submissions_data): ?>
            <section>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Submission ID</th>
                            <th>Date/Time</th>
                            <th>Mechanism ID</th>
                            <th>User Email</th>
                            <th>Input/Output Path</th>
                            <th>Code Path</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through the submissions data
                            while ($row = mysqli_fetch_assoc($submissions_data)) : 
                            $modal_id = "fileModal_" . $row['submission_id']; // Create a unique modal ID for each row
                            
                            // Get user email from the users table based on user_id
                            $user_id = $row['user_id'];
                            $email_query = "SELECT email FROM users WHERE UserID = '$user_id'";
                            $email_result = mysqli_query($link, $email_query);
                            $user_email = ($email_result && mysqli_num_rows($email_result) > 0) ? 
                                          mysqli_fetch_assoc($email_result)['email'] : 'Unknown';
                            
                            // Get the client_code from the mechanisms table to display the correct mechanism ID
                            $mechanism_id = $row['mechanism_id'];
                            $client_code_query = "SELECT client_code FROM mechanisms WHERE mechanism_id = '$mechanism_id'";
                            $client_code_result = mysqli_query($link, $client_code_query);
                            $display_mechanism_id = ($client_code_result && mysqli_num_rows($client_code_result) > 0) ? 
                                                    mysqli_fetch_assoc($client_code_result)['client_code'] : $mechanism_id;
                            ?>

                                <!-- initialize variables to hold the input and output paths to call on later -->
                                <tr>
                                    <td><?php echo $row['submission_id']; ?></td>
                                    <td><?php echo isset($row['created_at']) ? $row['created_at'] : date('Y-m-d H:i:s'); ?></td>
                                    <td><?php echo $display_mechanism_id; ?></td>
                                    <td><?php echo $user_email; ?></td>
                                    <td><button class='btn btn-info' data-toggle='modal' data-target='#<?php echo $modal_id ?>'>View
                                            Input/Output</button></td>
                                    <td><?php echo $row['code_path']; ?></td>
                                    <td>
                                        <form method='POST' style='display:inline;'>
                                            <input type='hidden' name='deleteSubmissionID' value='<?php echo $row['submission_id']; ?>'>
                                            <button type='submit' class='btn btn-danger btn-sm' onclick="return confirm('Are you sure you want to delete this submission?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <!-- Modal -->
                                <div class="modal fade" id="<?php echo $modal_id?>" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="fileModalLabel">File Content</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <h5>Input Data</h5>
                                                <pre class="bg-light p-3 rounded">
                                                    <?php
                                                    // The current format for the submissions files is "in-XXX.txt", which is hard to query for when the mechanism ids are like all double digits lol
                                                    // So we need to sanitize the mechanism id to be 3 digits long, which we can then tell PHP to look for in the submissions directory
                                                    $submission_id = $row['submission_id'];
                                                    
                                                    $mechanism_id = $row['mechanism_id'];
                                                    
                                                    $user_id = $row['user_id'];
                                    
                                                    $submission_directory_path = realpath("../../files/submissions");

                                                    // Get the client_code from the mechanisms table
                                                    $client_code_query = "SELECT client_code FROM mechanisms WHERE mechanism_id = '$mechanism_id'";
                                                    $client_code_result = mysqli_query($link, $client_code_query);
                                                    $client_code = ($client_code_result && mysqli_num_rows($client_code_result) > 0) ? 
                                                                   mysqli_fetch_assoc($client_code_result)['client_code'] : str_pad($mechanism_id, 3, '0', STR_PAD_LEFT);
                                                    
                                                    $inputPath = sprintf("%s/%d_%d_%d/in-%s.dat", $submission_directory_path, $submission_id, $mechanism_id, $user_id, $client_code);
                                                    
                                                    if (file_exists($inputPath)) {
                                                        echo file_get_contents($inputPath);
                                                    } else {
                                                        echo "File not found: $inputPath";
                                                    }
                                                    ?>
                                                </pre>
                                            </div>
                                            <div class="modal-body">
                                                <h5>Output Data</h5>
                                                <pre class="bg-light p-3 rounded">
                                                    <?php
                                                    $outputPath = sprintf("%s/%d_%d_%d/out-%s.dat", $submission_directory_path, $submission_id, $mechanism_id, $user_id, $client_code);
                                                    
                                                    if (file_exists($outputPath)) {
                                                        echo file_get_contents($outputPath);
                                                    } else {
                                                        echo "File not found: $outputPath";
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
                        <?php endwhile; ?>   
                    </tbody>
                </table>
            </section>
        </div>
        <?php endif; ?>
    </main>
    

    <script>
        function submitMakeSubmission() {
            const selectedCode = document.getElementById('algorithm-options').value;
            document.getElementById('mid-input').value = selectedCode;
            document.querySelector('form').submit();
        }

        $('#fileModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var filePath = button.data('filepath');
            var fileType = button.data('filetype');
            var modal = $(this);
            modal.find('.modal-title').text('File Content (' + fileType + ')');
        });
    </script>

    <!-- Add console logs for log messages -->
<?php
if (!empty($_SESSION['log_messages'])) {
    foreach ($_SESSION['log_messages'] as $logMessage) {
        echo "<script>console.log('" . addslashes($logMessage) . "');</script>";
    }
    unset($_SESSION['log_messages']); // Clear log messages after displaying
}
?>
</body>
</html>

<style>
    /* Custom button styling */
    .custom-btn {
        display: inline-block; /* Makes the button inline-block to allow margin manipulation */
        padding: 15px 100px; /* Adds padding inside the button for better size and spacing */
        background: linear-gradient(45deg, #007bff, #00c6ff); /* Adds a gradient background */
        color: white; /* Sets the text color to white */
        font-size: 25px; /* Sets the font size of the button text */
        font-weight: bold; /* Makes the button text bold */
        text-align: center; /* Centers the text inside the button */
        border-radius: 8px; /* Rounds the corners of the button */
        text-decoration: none; /* Removes any text underline */
        transition: all 0.3s ease; /* Adds smooth transition for hover effects */
        margin-left: 100px; /* Shifts the button 100px to the right */
    }

    /* Hover effect for the button */
    .custom-btn:hover {
        background: linear-gradient(45deg, #007bff, #00c6ff); /* Keeps the background gradient the same on hover */
        transform: scale(1.05); /* Slightly enlarges the button when hovered */
        color: white; /* Ensures the text color stays white */
    }   

    /* Styling for the container */
    .container {
        background-color: white; /* Sets the background color of the container */
        border-radius: 10px; /* Rounds the corners of the container */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Adds shadow to make the container look lifted */
        padding: 30px; /* Adds padding inside the container */
        margin-bottom: 30px; /* Adds space below the container */
        margin-top: 20px; /* Adds space above the container */
        background: linear-gradient(135deg, #f3f4f6, #ffffff); /* Adds a subtle gradient to the container */
    }

    /* Global body styling */
    body {
        background-color: #f4f7fa; /* Light background color for the page */
        margin: 0; /* Removes default body margin */
        padding: 0; /* Removes default body padding */
    }

    /* Button styling for the danger (delete) button */
    button.btn-danger {
        border-radius: 5px; /* Rounds the corners of the button */
        transition: background-color 0.3s ease; /* Smooth transition for background color change */
        border: none; /* Removes the default border around the button */
    }

    /* Hover effect for the danger button */
    button.btn-danger:hover {
        background-color: #97233f; /* Changes the background color when hovered */
    }

    /* Label styling for form groups */
    .form-group label {
        font-size: 1.1em; /* Increases the font size of the label */
        font-weight: 500; /* Makes the label text semi-bold */
    }

    /* Additional styling for form input fields */
    .form-group .form-control {
        padding-left: 30px; /* Adds extra padding to the left side of input fields */
    }

    /* General styling for form control elements like text inputs and selects */
    .form-control, .form-select {
        padding: 10px; /* Adds padding inside the input fields */
        font-size: 1em; /* Sets font size to standard size */
        border-radius: 6px; /* Rounds the corners of input fields */
        border: 1px solid #ccc; /* Sets a light gray border */
        width: 100%; /* Makes the input field fill its container */
        transition: all 0.3s ease; /* Adds smooth transition for focus effects */
    }

    /* Adjust the padding on the right side of the dropdown */
    .form-select {
        padding-right: 40px;  /* Adds more space for the dropdown arrow */
    }

    /* Focus effect for input fields */
    .form-control:focus, .form-select:focus {
        border-color: #007bff; /* Changes the border color to blue on focus */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Adds a blue shadow effect on focus */
    }

    /* Table styling */
    table {
        width: 100%; /* Makes the table width fill its container */
        border-collapse: collapse; /* Ensures table borders collapse into a single line */
        margin-top: 20px; /* Adds space above the table */
    }

    /* Table cell and header styling */
    th, td {
        padding: 12px 15px; /* Adds padding inside table cells */
        text-align: left; /* Aligns text to the left inside table cells */
        border: 1px solid #ddd; /* Sets a light gray border for the cells */
        transition: background-color 0.3s ease; /* Adds smooth transition for background color change */
    }

    th {
        background-color: #007bff; /* Sets a blue background for table headers */
        color: white; /* Sets the header text color to white */
        font-size: 1.1em; /* Increases font size of header */
    }

    /* Info button styling */
    button.btn-info {
        background-color: #17a2b8; /* Sets a blue background color */
        color: white; /* Sets the text color to white */
        border-radius: 5px; /* Rounds the corners of the button */
    }

    /* Table data font size */
    td {
        font-size: 1.1em; /* Increases the font size of table data */
    }

    /* Modal body for content formatting */
    .modal-body {
    font-size: 20px; /* Adjust font size for readability */
    padding: 20px; /* Add padding around the modal content */
    overflow: auto; /* Allow scrolling if the content is too large */
    max-height: 400px; /* Limit height for large content */
    }

    /* Modal header styling */
    .modal-header {
        background-color: #007bff; /* Sets a blue background for the modal header */
        color: white; /* Sets the header text color to white */
        border-top-left-radius: 10px; /* Rounds the top left corner */
        border-top-right-radius: 10px; /* Rounds the top right corner */
    }

    /* Modal body styling */
    .modal-body h5 {
        font-size: 1.1em; /* Increases the font size of the modal body header */
        color: #007bff; /* Sets the text color to blue */
    }

    /* Code block styling in modal */
    .modal-body pre {
    	white-space: pre-wrap; /* Ensures text wraps instead of overflowing */
    }

    /* To fix format issue, add a forced line break at the beginning using a pseudo-element */
    .modal-body pre::before {
    content: "---------------------------------------------------------";  /* Inserts a newline character at the start */
    white-space: pre; /* Ensures the newline appears properly */
    }

    /* To fix format issue, add a forced line break at the end using a pseudo-element */
    .modal-body pre::after {
    content: "---------------------------------------------------------";  /* Inserts a newline character at the end */
    white-space: pre; /* Ensures the newline appears properly */
    }



</style>