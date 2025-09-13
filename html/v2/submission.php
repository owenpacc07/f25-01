<?php

/**
 * This file handles submission of user input for a mechanism.
 * It contains two POST requests, one for editing a submission and one for creating a new submission.
 * The code creates a new submission record in the database, creates a folder for the submission files,
 * writes the input, output and code files to the folder, and updates the submission record in the database.
 * If successful, it redirects the user to their personal page.
 */
// Check if the request method is POST and the 'editSubmissionSubmit' button is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editSubmissionSubmit'])) {
    // Include the 'config-legacy.php' file to establish a database connection
    require_once 'config-legacy.php';
    // Start a new session
    session_start();
    // Get the user ID from the session
    $user = $_SESSION['userid'];
    // Get the client code from the POST data
    $client_code = $_POST["edit-mechanism"];
    // Get the edit submission ID from the POST data
    $edit_submission_id = $_POST['edit-submission-id'];
    // Retrieve the mechanism ID from the database based on the client code
    $mechanism = mysqli_fetch_all(mysqli_query($link, "select mechanism_id from mechanisms where client_code=$client_code"))[0][0];
    // Define file paths for input, output, and code
    $inputFilePath = "../input";
    $outputFilePath = "../output";
    $codeFilePath = "../code";
    $restrict_view = 0;
    // Create an SQL query to insert a new submission into the database
    $submission_insert = "insert into submissions (mechanism_id,user_id,input_path,output_path,code_path,restrict_view) values ($mechanism,$user,'$inputFilePath','$outputFilePath','$codeFilePath',$restrict_view);";
    // Check if the submission insertion was successful
    if ($link->query($submission_insert) === TRUE) {
        // Get the ID of the newly inserted submission
        $submission_id = $link->insert_id;
        // Create a folder for the submission
        $folder = "../files/submissions/" . $submission_id . "_" . $mechanism . "_" . $user . "/";
        mkdir($folder, 0770);

        // Write to the input file
        $inputFilePath = $folder . "input.dat";
        $inputfile = fopen($inputFilePath, "w") or die("Unable to open input file!");
        $txt = $_POST['edit-input-area'];
        if ($txt == '') {
            $default_file = "../files/core-a/m-" . $mechanism_code . "/in-" . $mechanism_code . ".dat";
            fwrite($inputfile, file_get_contents($default_file));
        } else {
            fwrite($inputfile, $txt);
        }
        fclose($inputfile);

        // Update the input file path in the database
        $update_query = "update submissions set input_path = '$inputFilePath' where submission_id=$submission_id;";
        $result = mysqli_query($link, $update_query);

        // Write to the output file
        $outputFilePath = $folder . "output.dat";
        $outputfile = fopen($outputFilePath, "w") or die("Unable to open output file!");
        $txt = $_POST["edit-output-area"];
        if ($txt == '') {
            $default_file = "../files/core-a/m-" . $mechanism_code . "/out-" . $mechanism_code . ".dat";
            fwrite($outputfile, file_get_contents($default_file));
        } else {
            fwrite($outputfile, $txt);
        }
        fclose($outputfile);

        // Update the output file path in the database
        $update_query = "update submissions set output_path = '$outputFilePath' where submission_id=$submission_id;";
        $result = mysqli_query($link, $update_query);

        // Write to the code file
        $codeFilePath = $folder . "code.java";
        $codefile = fopen($codeFilePath, "w") or die("Unable to open code file!");
        $txt = $_POST["edit-code-area"];
        if ($txt == '') {
            $restrict_view = 1;
            $default_file = "../cgi-bin/core/m-" . $mechanism_code . "/m" . $mechanism_code . ".java";
            fwrite($codefile, file_get_contents($default_file));
        } else {
            $restrict_view = 0;
            fwrite($codefile, $txt);
        }
        fclose($codefile);

        // Update the code file path and restrict_view in the database
        $update_query = "update submissions set code_path = '$codeFilePath', restrict_view=$restrict_view where submission_id=$submission_id;";
        $result = mysqli_query($link, $update_query);

        // Redirect to the 'myPage.php' page
        header("Location: ./myPage.php");
    } else {
        echo "Error: " . $link->error;
    }
}

// Check if the request method is POST and the 'submissionSubmit' button is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submissionSubmit'])) {
    // Include the 'config-legacy.php' file to establish a database connection
    require_once 'config-legacy.php';
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
    // Define file paths for input, output, and code
    $inputFilePath = "../input";
    $outputFilePath = "../output";
    $codeFilePath = "../code";
    $restrict_view = 0;
    // Create an SQL query to insert a new submission into the database
    $submission_insert = "insert into submissions (mechanism_id,user_id,input_path,output_path,code_path,restrict_view) values ($mechanism,$user,'$inputFilePath','$outputFilePath','$codeFilePath',$restrict_view);";
    // Check if the submission insertion was successful
    if ($link->query($submission_insert) === TRUE) {
        // Get the ID of the newly inserted submission
        $submission_id = $link->insert_id;
        // Create a folder for the submission
        $folder = "../files/submissions/" . $submission_id . "_" . $mechanism . "_" . $user . "/";
        mkdir($folder, 0770);

        // Write to the input file
        $inputFilePath = $folder . "input.dat";
        $inputfile = fopen($inputFilePath, "w") or die("Unable to open input file!");
        $txt = $_POST['input-area'];
        if ($txt == '') {
            $default_file = "../files/core-a/m-" . $mechanism_code . "/in-" . $mechanism_code . ".dat";
            fwrite($inputfile, file_get_contents($default_file));
        } else {
            fwrite($inputfile, $txt);
        }
        fclose($inputfile);

        // Update the input file path in the database
        $update_query = "update submissions set input_path = '$inputFilePath' where submission_id=$submission_id;";
        $result = mysqli_query($link, $update_query);

        // Write to the output file
        $outputFilePath = $folder . "output.dat";
        $outputfile = fopen($outputFilePath, "w") or die("Unable to open output file!");
        $txt = $_POST["output-area"];
        if ($txt == '') {
            $default_file = "../files/core-a/m-" . $mechanism_code . "/out-" . $mechanism_code . ".dat";
            fwrite($outputfile, file_get_contents($default_file));
        } else {
            fwrite($outputfile, $txt);
        }
        fclose($outputfile);

        // Update the output file path in the database
        $update_query = "update submissions set output_path = '$outputFilePath' where submission_id=$submission_id;";
        $result = mysqli_query($link, $update_query);

        // Write to the code file
        $codeFilePath = $folder . "code.java";
        $codefile = fopen($codeFilePath, "w") or die("Unable to open code file!");
        $txt = $_POST["file_text"];
        if ($txt == '') {
            $restrict_view = 1;
            $default_file = "../cgi-bin/core/m-" . $mechanism_code . "/m" . $mechanism_code . ".java";
            fwrite($codefile, file_get_contents($default_file));
        } else {
            $restrict_view = 0;
            fwrite($codefile, $txt);
        }
        fclose($codefile);

        // Update the code file path and restrict_view in the database
        $update_query = "update submissions set code_path = '$codeFilePath', restrict_view=$restrict_view where submission_id=$submission_id;";
        $result = mysqli_query($link, $update_query);

        // Redirect to the 'myPage.php' page
        header("Location: ./myPage.php");
    } else {
        echo "Error: " . $link->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.css">
    </link>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.3/theme/darcula.min.css"
        integrity="sha512-kqCOYFDdyQF4JM8RddA6rMBi9oaLdR0aEACdB95Xl1EgaBhaXMIe8T4uxmPitfq4qRmHqo+nBU2d1l+M4zUx1g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.3/mode/clike/clike.js"></script>

    <title>Submission Page</title>

</head>

<body onload="showAsCode()">
    <!-- Start of HTML code -->

    <?php include './navbar.php'; ?>

    <br><br>

    <?php
    // Include 'config-legacy.php' to establish a database connection
    require_once 'config-legacy.php';

    // Query to select component names
    $component_query = "Select component_name from components;";
    $components = mysqli_query($link, $component_query);

    // Query to select component names and corresponding algorithms
    $algorithm_query = "select components.component_name, mechanisms.algorithm from components inner join mechanisms on components.component_id = mechanisms.component_id;";
    $algorithms = mysqli_query($link, $algorithm_query);
    ?>

    <!-- Start of JavaScript code -->
    <script>
        // JavaScript function to display save forms based on user selection
        function displaySaveForms() {
            // Get the input, output, and code checkboxes
            inputCheck = document.getElementById("InputCheck");
            outputCheck = document.getElementById("OutputCheck");
            codeCheck = document.getElementById("CodeCheck");

            // Show/hide input container and remove/add 'disabled' class from submit button based on checkbox status
            if (inputCheck.checked) {
                document.getElementById("input-container").classList.remove("d-none");
                document.getElementById("submit-button").classList.remove("disabled");
            } else {
                document.getElementById("input-container").classList.add("d-none");
            }

            // Show/hide output container and remove/add 'disabled' class from submit button based on checkbox status
            if (outputCheck.checked) {
                document.getElementById("output-container").classList.remove("d-none");
                document.getElementById("submit-button").classList.remove("disabled");
            } else {
                document.getElementById("output-container").classList.add("d-none");
            }

            // Show/hide code container and remove/add 'disabled' class from submit button based on checkbox status
            if (codeCheck.checked) {
                document.getElementById("code-container").classList.remove("d-none");
                document.getElementById("submit-button").classList.remove("disabled");
            } else {
                document.getElementById("code-container").classList.add("d-none");
            }

            // If no checkboxes are selected, add 'disabled' class to submit button
            if (!inputCheck.checked && !outputCheck.checked && !codeCheck.checked) {
                document.getElementById("submit-button").classList.add("disabled");
            }
        }

        // JavaScript function to display the appropriate forms based on submission type
        function displayForms() {
            // Get the selected submission type
            const subType = document.getElementById("submission-options").options[document.getElementById("submission-options").selectedIndex].value;

            // If the submission type is 'Save', show input, output, and code checkboxes
            if (subType == "Save") {
                document.getElementById("input-check-container").classList.remove("d-none");
                document.getElementById("output-check-container").classList.remove("d-none");
                document.getElementById("code-check-container").classList.remove("d-none");
                document.getElementById("InputCheck").disabled = false;
                document.getElementById("OutputCheck").disabled = false;
                document.getElementById("CodeCheck").disabled = false;
                displaySaveForms();
            } else {
                // Hide input, output, and code checkboxes and disable them
                document.getElementById("input-check-container").classList.add("d-none");
                document.getElementById("output-check-container").classList.add("d-none");
                document.getElementById("code-check-container").classList.add("d-none");
                document.getElementById("InputCheck").checked = false;
                document.getElementById("OutputCheck").checked = false;
                document.getElementById("CodeCheck").checked = false;
                document.getElementById("InputCheck").disabled = true;
                document.getElementById("OutputCheck").disabled = true;
                document.getElementById("CodeCheck").disabled = true;
                displaySaveForms();

                // Depending on the submission type, show/hide input, output, and code containers
                if (subType == "Run") {
                    document.getElementById("input-container").classList.remove("d-none");
                    document.getElementById("output-container").classList.add("d-none");
                    document.getElementById("code-container").classList.remove("d-none");
                    document.getElementById("submit-button").classList.remove("disabled");
                } else if (subType == "Visualize") {
                    document.getElementById("input-container").classList.remove("d-none");
                    document.getElementById("output-container").classList.remove("d-none");
                    document.getElementById("code-container").classList.add("d-none");
                    document.getElementById("submit-button").classList.remove("disabled");
                } else {
                    // If none of the above, hide all containers and disable the submit button
                    document.getElementById("input-container").classList.add("d-none");
                    document.getElementById("output-container").classList.add("d-none");
                    document.getElementById("code-container").classList.add("d-none");
                    document.getElementById("submit-button").classList.add("disabled");
                }
            }
        }

        // JavaScript function to initialize CodeMirror for code editing
        function showAsCode() {
            if (document.getElementById("edit-code-area")) {
                CodeMirror.fromTextArea(document.getElementById("edit-code-area"), {
                    lineNumbers: true,
                    tabSize: 2,
                    mode: "text/x-java",
                    theme: "darcula"
                });
            }
        }

        // JavaScript function to display a text box for file selection and CodeMirror for code preview
        function showTextBox(input) {
            let file_text = document.getElementById("file_text");
            file_text.style.overflowX = "block";
            file_text.style.overflowY = "scroll";
            file_text.style.display = "none";
            var file = input.files[0];
            var reader = new FileReader();
            reader.onload = function (e) {
                file_text.value = e.target.result;
                CodeMirror(document.querySelector('#codePreview'), {
                    lineNumbers: true,
                    tabSize: 2,
                    value: e.target.result,
                    mode: "text/x-java",
                    theme: "darcula"
                }).on('change', function (cm) {
                    file_text.value = cm.getValue();
                });
            };
            reader.readAsText(file);
        }
    </script>
    <!-- End of JavaScript code -->

    <div class="container-fluid">
        <!-- Edit Submission Form -->
        <?php
        // Start a new session
        session_start();
        $user = $_SESSION['userid'];
        if (is_null($user)):
            header("Location: login.php");
        elseif (isset($_GET["submission_id"])) :
            require_once 'config-legacy.php';

            $submission_id = $_GET["submission_id"];

            $user_query = "select user_id from submissions where submission_id=$submission_id and user_id=$user;";
            $user_result = mysqli_query($link, $user_query);
            if (mysqli_num_rows($user_result) == 0):
                ?>
                <h3 style="color:red">Access Denied</h3>
                <?php header("Location: login.php"); ?>
            <?php else:
                $mechanism_info = "select mechanisms.client_code as mechanism, components.component_name as component, 
                mechanisms.algorithm as algorithm from components right join (mechanisms right join submissions 
                on mechanisms.mechanism_id=submissions.mechanism_id) on components.component_id = mechanisms.component_id 
                where submission_id=$submission_id;";
                $info_result = mysqli_query($link, $mechanism_info);
                while ($row = mysqli_fetch_assoc($info_result)) {
                    $mechanism = $row['mechanism'];
                    $component = $row['component'];
                    $algorithm = $row['algorithm'];
                }
                ?>

                <!-- Edit Submission Form -->
                <form class="m-2" method="post" enctype="multipart/form-data"
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <h2>Edit Submission</h2>
                    <input name="edit-submission-id" id="edit-submission-id" hidden
                        value="<?php echo $_GET['submission_id'] ?>"></input>
                    <input name="edit-mechanism" id="edit-mechanism" hidden value="<?php echo $mechanism ?>"></input>
                    <dl class="row">
                        <dt class="col-xl-1">Mechanism:</dt>
                        <dd class="col-xl-3">
                            <?php echo $mechanism ?>
                        </dd>
                    </dl>
                    <dl class="row">
                        <dt class="col-xl-1">Component:</dt>
                        <dd class="col-xl-3">
                            <?php echo $component ?>
                        </dd>
                    </dl>
                    <dl class="row">
                        <dt class="col-xl-1">Algorithm:</dt>
                        <dd class="col-xl-3">
                            <?php echo $algorithm ?>
                        </dd>
                    </dl>
                    <div class="form-row">
                        <?php
                        $input_query = "select submissions.input_path as filepath from submissions where submissions.submission_id = $submission_id;";
                        $input_result = mysqli_query($link, $input_query);
                        while ($row = mysqli_fetch_assoc($input_result)) {
                            $input_filepath = $row['filepath'];
                        }
                        ?>
                        <div id="input-container" class="form-group col-md-6 col-12 mt-2 mb-2">
                            <label for="input">Input:</label>
                            <textarea class="form-control" id="edit-input-area" name="edit-input-area"
                                rows="10"><?php echo file_get_contents($input_filepath) ?></textarea>
                        </div>
                        <?php
                        $output_query = "select submissions.output_path as filepath from submissions where submissions.submission_id = $submission_id;";
                        $output_result = mysqli_query($link, $output_query);
                        while ($row = mysqli_fetch_assoc($output_result)) {
                            $output_filepath = $row['filepath'];
                        }
                        ?>
                        <div id="output-container" class="form-group col-md-6 col-12 mt-2 mb-2">
                            <label for="output">Output:</label>
                            <textarea class="form-control" id="edit-output-area" name="edit-output-area"
                                rows="10"><?php echo file_get_contents($output_filepath) ?></textarea>
                        </div>
                        <?php
                        $code_query = "select submissions.code_path as filepath, submissions.restrict_view as restrict_view from submissions where submissions.submission_id = $submission_id;";
                        $code_result = mysqli_query($link, $code_query);
                        while ($row = mysqli_fetch_assoc($code_result)) {
                            $code_filepath = $row['filepath'];
                            $restrict_view = $row['restrict_view'];
                        }
                        if (!$restrict_view):
                            ?>
                            <div id="code-container" class="form-group col-12 mt-2 mb-2">
                                <label for="output">Code:</label>
                                <textarea class="form-control" id="edit-code-area" name="edit-code-area" rows="10"
                                    onload="showAsCode()"><?php echo file_get_contents($code_filepath) ?></textarea>
                            </div>
                        <?php else:
                            ?>
                            <div id="code-container" class="form-group col-12 mt-2 mb-2">
                                <label for="code">Code:</label>
                                <br>
                                <p>You are using default code!</p>
                                <br>
                                <input onchange="showTextBox(this)" type="file" id="file" size=70 name="file" size=70>
                                <br>
                                <textarea cols="90" rows="15" name="edit-file-text" id="file_text" style="display:none"></textarea>
                                <div id="codePreview" name="edit-code-text" style="display: block;"></div>
                            </div>
                            <?php
                        endif;
                        ?>
                    </div>

                    <button name="editSubmissionSubmit" id="edit-submit-button" type="submit"
                        class="btn btn-primary my-2">Submit</button>
                    <button type="button" onclick="window.location.replace('./myPage.php')"
                        class="btn btn-light my-2">Cancel</button>
                </form>
            <?php endif; ?>

            <!-- Make Submission Form  -->
        <?php else: ?>
            <h2>Make A Submission</h2>
            <form class="m-2" method="post" enctype="multipart/form-data"
                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-row">
                    <div class="form-group col-4 mt-2 mb-2">
                        <label name="algorithm" for="algorithm">Algorithm:</label>
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
                    <div class="form-group col-4 mt-2 mb-2">
                        <label name="submission" for="submission">Submission:</label>
                        <select class="form-select" id="submission-options" name="submission-options"
                            onchange="displayForms()">
                            <option>-----</option>
                            <option>Save</option>
                            <option>Run</option>
                            <option>Visualize</option>
                        </select>
                    </div>
                    <div id="input-check-container" class="form-check form-check-inline col-md-6 col-12 mt-2 mb-2 d-none">
                        <input class="form-check-input" type="checkbox" value="" id="InputCheck"
                            onchange="displaySaveForms()">
                        <label class="form-check-label" for="flexCheckDefault">
                            Input
                        </label>
                    </div>
                    <div id="output-check-container" class="form-check form-check-inline col-md-6 col-12 mt-2 mb-2 d-none">
                        <input class="form-check-input" type="checkbox" value="" id="OutputCheck"
                            onchange="displaySaveForms()">
                        <label class="form-check-label" for="flexCheckChecked">
                            Output
                        </label>
                    </div>
                    <div id="code-check-container" class="form-check form-check-inline col-md-6 col-12 mt-2 mb-2 d-none">
                        <input class="form-check-input" type="checkbox" value="" id="CodeCheck"
                            onchange="displaySaveForms()">
                        <label class="form-check-label" for="flexCheckChecked">
                            Code
                        </label>
                    </div>
                </div>
                <div class="form-row">
                    <div id="input-container" class="form-group col-md-6 col-12 mt-2 mb-2 d-none">
                        <label for="input">Input:</label>
                        <textarea class="form-control" id="input-area" name="input-area" rows="10"></textarea>
                    </div>
                    <div id="output-container" class="form-group col-md-6 col-12 mt-2 mb-2 d-none">
                        <label for="output">Output:</label>
                        <textarea class="form-control" id="output-area" name="output-area" rows="10"></textarea>
                    </div>
                    <div id="code-container" class="form-group col-12 mt-2 mb-2 d-none">
                        <label for="code">Code:</label>
                        <br>
                        <input onchange="showTextBox(this)" type="file" id="file" size=70 name="file" size=70>
                        <br>
                        <textarea cols="90" rows="45" name="file_text" id="file_text" style="display:none"></textarea>
                        <div id="codePreview" name="code_text" style="display: block;"></div>
                    </div>
                </div>

                <button name="submissionSubmit" id="submit-button" type="submit"
                    class="btn btn-primary mt-2 mb-2 disabled">Submit</button>
            </form>
        </div>
    <?php endif; ?>
    <!-- End of HTML code -->


</body>


</html>