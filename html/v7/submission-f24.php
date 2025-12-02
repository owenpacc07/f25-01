<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Form</title>
    <link rel="stylesheet" href="./CSS-stylesheets/submissions-style.css">
</head>

<?php 
    // Check if the form has been submitted and the 'algorithmSubmit' button has been clicked
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['algorithmSubmit'])) {
        // Include the 'config.php' file to establish a database connection
        require_once 'config.php';
        // Start a new session
        session_start();
        // Get the user ID from the session
        $user = $_SESSION['userid'];
        // Get the mechanism code from the POST data
        $mechanism_code = $_POST['algorithm-options'];
        // Create an SQL query to retrieve the mechanism ID based on the mechanism code
        $mechanism_query = "select mechanism_id from mechanisms where mechanisms.client_code = '$mechanism_code';";
        $mechanism_result = mysqli_query($link, $mechanism_query);
        $mechanism = mysqli_fetch_all($mechanism_result)[0][0];

        // Check if the mechanism was found
        if ($mechanism) {
            // Redirect the user to the ../core-s/$mechanism_code/edit.php page
            header("Location: ./core-s/m-$mechanism_code/edit.php");
            exit(); // Ensure no further code is executed after the redirect
        } else {
            // Handle the case where the mechanism was not found
            echo "Mechanism not found.";
        }
    }
?>

<body>

    <?php require_once 'config.php'; ?>

    <?php include 'navbar.php'; ?>

    <header style="display: flex; justify-content: center; align-items: center; background-color: #f8f9fa; padding: 20px; border-bottom: 1px solid #dee2e6;">
        <h1>Submit a Mechanism</h1>
    </header>
    
    <div class="submission-form">
        <form method="POST" action="">
            <!--Section for a dropdown menu to view the algorithms to select-->
            <div class="form-group">
                <label name="algorithm" for="algorithm">Algorithms:</label>
                <select class="form-select" id="algorithm-options" name="algorithm-options">
                    <option>-----</option>
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

            <!-- Section for a button that will submit the requested algorithm -->
            <button type="submit" class="btn btn-primary" name="algorithmSubmit">Make a Submission With This Algorithm</button>
        </form>
    <div>


</body>
</html>