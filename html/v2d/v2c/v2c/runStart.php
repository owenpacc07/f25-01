<?php
session_start();

$_SESSION['mechanismid'] = -1;

// header("Location: ../view.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['mechanismid'] = $_POST['Mechanism'];
    // if the codes input is 0, do a header to the location core/mechanismid

    if ($_POST['codes'] == 0) {
        $mechanismID = $_POST['mechanismid'];
        header("Location: core/m-" . $mechanismID . ".php");
    } else {
        header("Location: submission.php");
    }

    // Need to insert runs into Database in next version.

    // header("Location: editInputOutput.php");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="adminPanel/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</head>

<body>

    <?php
    include 'templates/navbar.php';
    ?>

    <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <h1> RUN a Mechanism </h1>

        <hr>

        <!-- What mechanism would you like to run?
            <select name="Mechanism" required>
                <option selected value="">Select Mechanism</option>
                <?php
                require_once 'config-legacy.php';
                $result = mysqli_query($link, "Select * from mechanisms;");
                while ($row = mysqli_fetch_array($result)) {
                    $mechanismValue = $row['mechanismID'];
                    $mechanismName = $row['Name'];
                    echo "<option value='$mechanismValue'>$mechanismName";
                }
                ?>
            </select> -->

        Mechanism ID: <input type="text" name="mechanismid" id="mechanismid" required>

        <p> [CPU Scheduling] 001 for nonpreemptive FCFS, 002 for nonpreemptive SJF, 003 for nonpreemptive SJF, 004 for nonpreemptive Priority, 005 for Round Robin, 006 for preemptive SJF, 007 for preemptive Priority
        <p> [Page Replacement ] 021 for FIFO, 022 for LRU, 023 for Optimal, 024 for LFU, 025 for MFU
        <p> [Disk Scheduling] 041 for FIFO, 042 for LRU, 043 for Optimal, 044 for LFU, 045 for MFU
        <p> *[Memory Allocation] 011 for First Fit, 012 for Best Fit, 013 for Worst Fit
        <p> *[File Allocation] 031 for Contiguous, 032 for Linked, 033 for Indexed



            <br>
            <br>

            <!-- make this div centered and only take up half of the screen. -->
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                </div>
                                <div class="col center-block text-center">
                                    System
                                </div>
                                <div class="col center-block text-center">
                                    Your Own
                                </div>
                            </div>

                            <div class="row">
                                <div class="col center-block text-center">
                                    Input
                                </div>
                                <div class="col center-block text-center">
                                    <input type='radio' name='input' value='0' checked>
                                </div>
                                <div class="col center-block text-center">
                                    <input type='radio' name='input' value='1'>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col center-block text-center">
                                    Output
                                </div>
                                <div class="col center-block text-center">
                                    <input type='radio' name='output' value='0' checked>
                                </div>
                                <div class="col center-block text-center">
                                    <input type='radio' name='output' value='1'>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col center-block text-center">
                                    Codes
                                </div>
                                <div class="col center-block text-center">
                                    <input type='radio' name='codes' value='0' checked>
                                </div>
                                <div class="col center-block text-center">
                                    <input type='radio' name='codes' value='1'>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4 center-block text-center">
                                    Visualize
                                </div>
                                <div class="col center-block text-center">
                                    <input type='checkbox' name='visualize' value='1'> <!-- checked -->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4 center-block text-center">
                                    Execute
                                </div>
                                <div class="col center-block text-center">
                                    <input type='checkbox' name='execute' value='1'> <!-- checked -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <input type="submit" value="Submit">

    </form>



</body>

</html>