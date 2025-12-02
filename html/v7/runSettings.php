<?php





require_once 'config-legacy.php';
global $link;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['settingsSubmit'])) {

    $mechanismID = $_SESSION['mechanismid'];
    $userid = $_SESSION['userid'];
    $input = $_POST['input'];
    $output = $_POST['output'];
    $codes = $_POST['code'];
    $visualize = $_POST['visualize'];
    $execute = $_POST['execute'];
    $status = 'complete';

    $conditional_query = "select * from runs where mechanismID = $mechanismID and userID = $userid";
    $conditional = mysqli_fetch_array(mysqli_query($link, $conditional_query));


    // if (mysqli_num_rows($conditional) > 0) {
    //     $update_query = "update runs set input = '$input', output = '$output', code = '$codes', visualize = '$visualize', execute = '$execute', status = '$status' where mechanismID = $mechanismID and userID = $userid";
    //     $update = mysqli_query($link, $update_query);
    //     debug_to_console($update);
    // } else {
    //     $insert_query = "insert into runs (mechanismID, userID, input, output, code, visualize, execute, status) values ($mechanismID, $userid, '$input', '$output', '$codes', '$visualize', '$execute', '$status')";
    //     $insert = mysqli_query($link, $insert_query);
    //     debug_to_console($insert);

    // header("location: index.php?from=sub-page");

    // $ins_query = "
    //     INSERT INTO runs
    //     (runID, mechanismID, userID, input, output, codes, visualize, execute, status) 
    //     VALUES (NULL, '$mechanismID', '$userid', '$input', '$output', '$codes', '$visualize', '$execute', '$status')";

    // $result = mysqli_query($link, $ins_query);
    // if ($result) {
    //     echo "Settings updated successfully";
    // } else {
    //     echo "Error: " . $ins_query . "<br>" . mysqli_error($link);
    // }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</head>

<body>

    <div class="container">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Run Settings
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="text-center modal-title" id="exampleModalLabel">Run Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" target="_self">
                        <div class="modal-body">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="settingsSubmit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</body>

</html>