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

if (isset($_SESSION['user_type'])) {
    $_SESSION['log_messages'][] = "User Type in session: " . $_SESSION['user_type'];
}

// Determine if the logged-in user is an admin based on userType
$isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 1;
$_SESSION['log_messages'][] = "User Role: " . ($isAdmin ? 'Admin' : 'User');

// Initialize the experiments data to null
$experiments_data = null;
$family_id = null;

// Check if the 'family_id' is set via GET or POST
if (isset($_POST['family_id']) || isset($_GET['family_id'])) {
    $family_id = mysqli_real_escape_string($link, $_POST['family_id'] ?? $_GET['family_id']);
    $_SESSION['log_messages'][] = "Selected Mechanism ID: " . $family_id;

    // Query the experiments table based on if admin or not
    if ($isAdmin) {
        $experiments_data = mysqli_query($link, "SELECT * FROM experiments WHERE family_id = '$family_id'");
    } else {
        $user_id = mysqli_real_escape_string($link, $_SESSION['userid']); // Escape user ID for safety
        $experiments_data = mysqli_query($link, "SELECT * FROM experiments WHERE user_id = '$user_id' AND family_id = '$family_id'");
    }

}

// If no 'family_id' is specified (or the search field is empty), fetch all experiments for the logged-in user (or all if admin)
if (!$family_id) {
    if ($isAdmin) {
        $experiments_data = mysqli_query($link, "SELECT * FROM experiments");
    } else {
        $user_id = mysqli_real_escape_string($link, $_SESSION['userid']);
        $experiments_data = mysqli_query($link, "SELECT * FROM experiments WHERE user_id = '$user_id'");
    }
}


// Logic to delete a experiment based on the experiment ID
if (isset($_POST['deleteExperimentID'])) {
    $deleteExperimentID = $_POST['deleteExperimentID'];

    // Fetch the folder name based on experiment id_mechanism id_user id
    $fetchQuery = "SELECT family_id, user_id FROM experiments WHERE experiment_id = '$deleteExperimentID'";
    $fetchResult = mysqli_query($link, $fetchQuery);

    if ($fetchResult && mysqli_num_rows($fetchResult) > 0) {
        $row = mysqli_fetch_assoc($fetchResult);
        $family_id = $row['family_id'];
        $user_id = $row['user_id'];
        $folderName = "{$deleteExperimentID}_{$family_id}_{$user_id}";

        // Specify the base directory
        $baseDirectory = '/var/www/projects/f24-02/html/files/experiments/';
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

        // Delete the experiment from the database
        $deleteQuery = "DELETE FROM experiments WHERE experiment_id = '$deleteExperimentID'";
        $deleteResult = mysqli_query($link, $deleteQuery);

        if (!$deleteResult) {
            $_SESSION['log_messages'][] = "Error deleting experiment from database: " . mysqli_error($link);
        } else {
            // Redirect to the same page without any filtering by 'family_id'
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
    $mechanism_query = "select mechanism_id from comparisons where comparisons.client_code = '$mechanism_code';";
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Run a Mechanism</title>
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
        <p> Only Works For Page Replacement For Now</p>
	<p> Can Edit Data For Page Replacement, And It Will Update in /page-rep/index.php, Output File is Useless as of Right Now</p>
        <div class="container">
            <form method="POST" action="">
                <!--Section for a dropdown menu to view the algorithms to select-->
                <div class="form-group">
                    <label name="algorithm" for="algorithm">Algorithm:</label>
                    <select class="form-select" id="algorithm-options" name="algorithm-options">
                        <?php
                            $algorithm_query = "select comparisons.client_code as client_code, components.component_name as component, comparisons.algorithm as algorithm from components inner join comparisons on components.component_id = comparisons.component_id order by client_code;";
                            $algorithm_result = mysqli_query($link, $algorithm_query);
                            while ($row = mysqli_fetch_assoc($algorithm_result)):
                        ?>
                        <option value="<?php echo $row['client_code'] ?>">
                        <?php echo $row['client_code'] . " " . $row['component']  ?>
                        </option>
                        <?php
                            endwhile;
                        ?>
                    </select>
                </div>

                <!-- Section for a button that will submit the requested algorithm -->
                <button type="submit" class="btn custom-btn" name="algorithmSubmit">Make a Submission With This Algorithm</button>
            </form>
        </div>
        <div class="container">
            <form method="POST" class="form-inline">
            <div class="form-group mb-2">
                <h2>Search for a submission by mechanism ID</h2>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <input type="text" class="form-control" id="family_id" name="family_id" placeholder="Enter a mechanism ID"> 
		    </div>
            <button type="submit" class="btn btn-primary mb-2">Search</button>
            </form>
        </div>
        <?php if ($experiments_data): ?>
            <section>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Submission ID</th>
                            <th>Mechanism ID</th>
                            <th>User ID</th>
                            <th>Input/Output Path</th>
                            <th>Code Path</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through the experiments data
                            while ($row = mysqli_fetch_assoc($experiments_data)) : 
                            $modal_id = "fileModal_" . $row['experiment_id']; // Create a unique modal ID for each row
                            ?>

                                <!-- initialize variables to hold the input and output paths to call on later -->
                                <tr>
                                    <td><?php echo $row['experiment_id']; ?></td>
                                    <td><?php echo $row['family_id']; ?></td>
                                    <td><?php echo $row['user_id']; ?></td>
                                    <td><button class='btn btn-info' data-toggle='modal' data-target='#<?php echo $modal_id?>'>View Input/Output</button></td>
                                    <td><?php echo $row['code_path']; ?></td>
                                    <td>
                                        <form method='POST' style='display:inline;'>
                                            <input type='hidden' name='deleteExperimentID' value='<?php echo $row['experiment_id']; ?>'>
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
                                                    // The current format for the experiments files is "in-XXX.txt", which is hard to query for when the mechanism ids are like all double digits lol
                                                    // So we need to sanitize the mechanism id to be 3 digits long, which we can then tell PHP to look for in the experiments directory
                                                    $experiment_id = $row['experiment_id'];
                                                    
                                                    $family_id = $row['family_id'];
                                                    
                                                    $user_id = $row['user_id'];
                                    
                                                    $experiment_directory_path = realpath("../../files/experiments");

                                                    $inputPath = sprintf("%s/%d_%d_%d/in-%03d.dat", $experiment_directory_path, $experiment_id, $family_id, $user_id, $family_id);
                                                    
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
                                                    $outputPath = sprintf("%s/%d_%d_%d/out-%03d.dat", $experiment_directory_path, $experiment_id, $family_id, $user_id, $family_id);
                                                    
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
    display: inline-block;
    padding: 15px 100px;
    background: linear-gradient(45deg, #007bff, #00c6ff);
    color: white;
    font-size: 25px;
    font-weight: bold;
    text-align: center;
    border-radius: 8px;  
    text-decoration: none;
    transition: all 0.3s ease;
}

.custom-btn:hover {
    background: linear-gradient(45deg, #007bff, #00c6ff);
    transform: scale(1.05);
    color: white;
}   
 
/* Container */
.container {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-bottom: 30px;
    margin-top: 20px;
    background: linear-gradient(135deg, #f3f4f6, #ffffff);
}

body {
    background-color: #f4f7fa;
    margin: 0;
    padding: 0;
}


button.btn-danger {
    border-radius: 5px; 
    transition: background-color 0.3s ease;
    border: none; /* Remove button border */
}

button.btn-danger:hover {
    background-color: #97233f; 
}

/* Form Elements */
.form-group label {
    font-size: 1.1em;
    font-weight: 500;
}


.form-group .form-control {
    padding-left: 30px;
}

.form-control, .form-select {
    padding: 10px;
    font-size: 1em;
    border-radius: 6px;
    border: 1px solid #ccc;
    width: 100%;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #ddd;
    transition: background-color 0.3s ease;
}

th {
    background-color: #007bff;
    color: white;
    font-size: 1.1em;
}

button.btn-info {
    background-color: #17a2b8;
    color: white;
    border-radius: 5px;
}

td {
    font-size: 1.1em;
}

.modal-content {
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.modal-header {
    background-color: #007bff;
    color: white;
    font-size: 1.2em;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.modal-footer {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.modal-body h5 {
    font-size: 1.1em;
    color: #007bff;
}

.modal-body pre {
    background-color: #f4f4f4;
    padding: 15px;
    border-radius: 8px;
    overflow: auto;
}



}



</style>