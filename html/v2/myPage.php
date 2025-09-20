<?php
require_once "config-legacy.php";
global $link;
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}



$userID = $_SESSION["userid"];

//echo "<div class='alert alert-danger' role='alert'> Current User: " .$userID. "</div>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // if the useremail is set then update the user to have that email
    // check that email does not already exist first
    if (isset($_POST['UserEmail']) && ($_POST['UserEmail'] != "")) {
        $email = $_POST['UserEmail'];
        $sql = "SELECT * FROM users WHERE Email = '$email'";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) == 0) {
            $sql = "UPDATE users SET Email = '$email' WHERE UserID = '$userID'";
            $result = mysqli_query($link, $sql);
            if ($result) {
                echo "<div class=\"toast align-items-center\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">
                <div class=\"d-flex\">
                  <div class=\"toast-body\">
                  Hello, world! This is a toast message.
                 </div>
                  <button type=\"button\" class=\"btn-close me-2 m-auto\" data-bs-dismiss=\"toast\" aria-label=\"Close\"></button>
                </div>
              </div>";
            } else {
                echo "<div class=\"toast align-items-center\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">
                <div class=\"d-flex\">
                  <div class=\"toast-body\">
                  Hello, world! This is a toast message.
                 </div>
                  <button type=\"button\" class=\"btn-close me-2 m-auto\" data-bs-dismiss=\"toast\" aria-label=\"Close\"></button>
                </div>
              </div>";
            }
        } else {
            echo "<div class='alert alert-danger' role='alert'>Email already exists</div>";
        }
    }
    // do the same for the password
    if (isset($_POST['UserCurrentPassword']) && isset($_POST['UserPasswordReset']) && isset($_POST['UserPasswordConfirm'])) {
        $currentPassword = $_POST['UserCurrentPassword'];
        $newPassword = $_POST['UserPasswordReset'];
        $passwordConfirm = $_POST['UserPasswordConfirm'];

        // Check if both new passwords are not empty and match
        if (!empty($newPassword) && !empty($passwordConfirm) && $newPassword === $passwordConfirm) {
            // Check if the current password matches the user's current password
            $sql = "SELECT Password FROM users WHERE UserID = '$userID'";
            $result = mysqli_query($link, $sql);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                $storedPassword = $row['Password'];
                //if (password_verify($currentPassword, $storedPassword)) {
                    // Current password is correct, proceed with the password reset
                    //$newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
                    //$updateSql = "UPDATE users SET Password = '$newPasswordHash' WHERE UserID = '$userID'";
                    //$updateResult = mysqli_query($link, $updateSql);

	    if ($currentPassword === $storedPassword) {
                	// Current password is correct, proceed with the password reset
                	$updateSql = "UPDATE users SET Password = '$newPassword' WHERE UserID = '$userID'";
                	$updateResult = mysqli_query($link, $updateSql);

                    if ($updateResult) {
                        echo "<div class=\"toast align-items-center\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">
                            <div class=\"d-flex\">
                              <div class=\"toast-body\">
                              Password reset successfully!
                             </div>
                              <button type=\"button\" class=\"btn-close me-2 m-auto\" data-bs-dismiss=\"toast\" aria-label=\"Close\"></button>
                            </div>
                          </div>";
                    } else {
                        echo "<div class=\"toast align-items-center\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">
                            <div class=\"d-flex\">
                              <div class=\"toast-body\">
                              Password reset failed. Please try again.
                             </div>
                              <button type=\"button\" class=\"btn-close me-2 m-auto\" data-bs-dismiss=\"toast\" aria-label=\"Close\"></button>
                            </div>
                          </div>";
                    }
                } else {
                    echo "<div class=\"toast align-items-center\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">
                        <div class=\"d-flex\">
                          <div class=\"toast-body\">
                          Current password is incorrect. Please try again.
                         </div>
                          <button type=\"button\" class=\"btn-close me-2 m-auto\" data-bs-dismiss=\"toast\" aria-label=\"Close\"></button>
                        </div>
                      </div>";
                }
            }
        } else {
            echo "<div class=\"toast align-items-center\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">
                <div class=\"d-flex\">
                  <div class=\"toast-body\">
                  Passwords do not match. Please make sure they match.
                 </div>
                  <button type=\"button\" class=\"btn-close me-2 m-auto\" data-bs-dismiss=\"toast\" aria-label=\"Close\"></button>
                </div>
              </div>";
        }
    }


    if (isset($_POST['deleteSubmission'])) {
        $submission_id = $_POST['delete-id'];
        require_once 'config-legacy.php';
        $filepaths = mysqli_fetch_all(mysqli_query($link, "select input_path as input, output_path as output, code_path as code from submissions where submission_id=$submission_id;"))[0];
        @$inputPath = $filepaths[0];
        @unlink($inputPath);
        @$outputPath = $filepaths[1];
        @unlink($outputPath);
        @$codePath = $filepaths[2];
        @unlink($codePath);
        @$filePath = substr($filepaths[0], 0, strrpos($filepaths[0], "/") + 1);
        @rmdir($filePath);
        $delete_query = "delete from submissions where submission_id=$submission_id;";
        $delete_result = mysqli_query($link, $delete_query);
    }

     if (isset($_POST['expdeleteSubmission'])) {
        $experiment_id = $_POST['exp-delete-id'];
        require_once 'config-legacy.php';
        $filepaths = mysqli_fetch_all(mysqli_query($link, "select input_path as input, output_path as output, code_path as code from experiments where experiment_id=$experiment_id;"))[0];
        @$inputPath = $filepaths[0];
        @unlink($inputPath);
        @$outputPath = $filepaths[1];
        @unlink($outputPath);
        @$codePath = $filepaths[2];
        @unlink($codePath);
        @$filePath = substr($filepaths[0], 0, strrpos($filepaths[0], "/") + 1);
        @rmdir($filePath);
        $delete_query = "delete from experiments where experiment_id=$experiment_id;";
        $delete_result = mysqli_query($link, $delete_query);
    }


    //When Visualize button pressed, get submission input and put it in advanced mode data file then bring user to given mechanism page
    if (isset($_POST['visualizeSubmit'])) {
        $submission_id = $_POST['submission_id'];
        $clientCode = $_POST['client_code'];
        $sql = "select client_code from mechanisms where mechanism_id = '$clientCode'";
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_assoc($result);
        echo $row['client_code'];
        $filepaths = mysqli_fetch_all(mysqli_query($link, "select input_path as input, output_path as output, code_path as code from submissions where submission_id=$submission_id;"))[0];
        @$inputPath = $filepaths[0];
        $advancedModePath = "../files/core-a/m-{$row['client_code']}/in-{$row['client_code']}.dat";

        $submissionInput = file_get_contents($inputPath);
        file_put_contents($advancedModePath, $submissionInput);

        $mid = $row['client_code'];
        header('Location: ./core-a' . $mid);
    }

    if (isset($_POST['runSubmit'])) {
        $submission_id = $_POST['submission_id'];
        $clientCode = $_POST['client_code'];
        $sql = "select client_code from mechanisms where mechanism_id = '$clientCode'";
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_assoc($result);
        echo $row['client_code'];
        $filepaths = mysqli_fetch_all(mysqli_query($link, "select input_path as input, output_path as output, code_path as code from submissions where submission_id=$submission_id;"))[0];
        @$inputPath = $filepaths[0];
        $advancedModePath = "../files/core-a/m-{$row['client_code']}/in-{$row['client_code']}.dat";

        $submissionInput = file_get_contents($inputPath);
        file_put_contents($advancedModePath, $submissionInput);

        $mid = $row['client_code'];
        header('Location: ./core-a/m-' . $mid);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./fileViewing.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <!-- 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    
    </script>
-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.css">
    </link>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php
    // get the email of $_SESSION["userid"]
    //$sql = "SELECT * FROM users WHERE UserID = '$_SESSION[userid]'";
    $sql = "SELECT * FROM users WHERE Email = '$_SESSION[email]'";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $email = $row["Email"];
    echo "<title>" . $email . "'s Page</title>";

    ?>
</head>


<body onload="load()">
    <script>
        let code = "";

        function load() {
            document.querySelectorAll("#runButton").forEach(function (element) {
                element.setAttribute("disabled", "disabled");
            });
        }

        function openModal(modalname) {
            console.log(modalname);
            document.getElementById(modalname).hidden = false;
        }

        function runFile(file) {
            console.log("run file");
            console.log(file);
            console.log(code);
            document.getElementById("output").style.display = "block";
            let response;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "https://emkc.org/api/v2/piston/execute/", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    response = JSON.parse(this.responseText);
                    console.log(response);
                    // save the run.output in a string
                    var output = response.run.output;
                    // display the output in the codeOutput textarea
                    console.log(output);
                    document.getElementById("output").innerHTML = output;
                }
            };
            xhr.send(JSON.stringify({
                "language": "java",
                "version": "15.0.2",
                "files": [{
                    "content": code
                }]
            }));
        }

        function openFile(file, id) {
            document.querySelectorAll("#openButton").forEach(function (element) {
                element.setAttribute("disabled", "disabled");
            });
            if (file === "CLOSE") {
                // remove the file from view and close it

                document.getElementById("code").innerHTML = "";
                //document.getElementById("saveChanges").style.display = "none";
                // make the open button active again
                document.querySelectorAll("#openButton").forEach(function (element) {
                    element.removeAttribute("disabled");
                });
                // close the file
                document.querySelectorAll("#runButton").forEach(function (element) {
                    element.setAttribute("disabled", "disabled");
                });
                document.getElementById("output").style.display = "none";
                document.getElementById("row" + id).classList.remove("show");
                return;
            }
            // set run button to active when file is opened
            document.querySelectorAll("#runButton").forEach(function (element) {
                element.removeAttribute("disabled");
            });

            fetch("../" + file)
                .then(response => response.text())
                .then(text => {
                    code = text;
                    CodeMirror(document.querySelector('#code'), {
                        lineNumbers: true,
                        value: text,
                        mode: "text/x-java",
                        theme: "material"
                    }).on('change', function (cm) {
                        // update the text of the currently open file
                        code = cm.getValue();

                    });

                });
        }

        function downloadFile(file) {
            openFile(file);
            let a = document.createElement("a");
            a.href = "../" + file;
            a.download = file;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function sendToEdit(id) {
            window.location.replace(`./submission.php?submission_id=${id}`);
        }
	function sendToExpEdit(id) {
	// Should be more dynamic, should be allowed to take ./core-c/m-00x/edit.php?experiment_id=${id}`
	// Can be something like this??? `./core-c/${section}/edit.php?experiment_id=${id}`
    	window.location.replace(`./core-c/m-003/edit.php?experiment_id=${id}`);
	}

    </script>
    <?php include './navbar.php'; ?>
    <!-- user data with options to edit -->
    <div class="container" style="margin-top:3rem;">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card text-white bg-secondary">
                    <div class="card-header">Change Info - Only Fill Out What You Want to Change</div>
                    <div class="card-body">
                        <!-- form to edit user data -->
                        <form method="POST">
                            <div class="form-group">
                                <label for="email">Change Email</label>
                                <input type="email" class="form-control" id="email" name="UserEmail"
                                    placeholder="Email">
                            </div>

                            <div class="form-group">
                                <label for="passwordCurrent">Current Password</label>
                                <input type="password" class="form-control" id="passwordCurrent"
                                    name="UserCurrentPassword" placeholder="Current Password">
                            </div>

                            <div class="form-group">
                                <label for="passwordReset">Change Password</label>
                                <input type="password" class="form-control" id="passwordReset" name="UserPasswordReset"
                                    placeholder="Password Reset">
                            </div>

                            <div class="form-group">
                                <label for="passwordConfirm">Confirm Password</label>
                                <input type="password" class="form-control" id="passwordConfirm"
                                    name="UserPasswordConfirm" placeholder="Password Confirm">
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <h1>My Submissions</h1>
    <!-- user submissions table -->
    <div class="row">
        <div class="col-12">
            <table class="table table-hover table-secondary">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date/Time</th>
                        <th scope="col">Mechanism</th>
                        <th scope="col">Component</th>
                        <th scope="col">Algorithm</th>
                        <th scope="col">Input</th>
                        <th scope="col">Output</th>
                        <th scope="col">Code</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select submissions.submission_id as id, components.component_name as component, 
                    mechanisms.algorithm as algorithm, mechanisms.client_code as client_code, 
                    submissions.input_path as input, 
                    submissions.output_path as output, 
                    submissions.code_path as code, 
                    submissions.restrict_view as restrict_view,
                    submissions.created_at as created_at from submissions
                    right join (mechanisms right join components on mechanisms.component_id=components.component_id) 
                    on submissions.mechanism_id=mechanisms.mechanism_id where 
                    submissions.user_id=$userID order by submissions.submission_id;";
                    $result = mysqli_query($link, $sql);
                    if ($result) :
                        while ($row = mysqli_fetch_assoc($result)) :
                            //OLD FORMATING
                            // $filePath = "files/" . $row['filePath'];
                            // echo $filePath;
                            // $id = $row['SubID'];
                            // $owner_result = mysqli_query($link, "select Email from users where UserID = $row[Owner]");
                            // $owner = mysqli_fetch_array($owner_result)[0];
                    
                            // $mechanism_result = mysqli_query($link, "select Name from mechanisms where mechanismID = $row[Mechanism]");
                            // $mechanism = mysqli_fetch_array($mechanism_result)[0];
                            ?>
                            <tr>
                                <td>
                                    <?php echo $row['id'] ?>
                                </td>
                                <td>
                                    <?php echo isset($row['created_at']) ? $row['created_at'] : date('Y-m-d H:i:s'); ?>
                                </td>
                                <td>
                                    <?php echo $row['client_code'] ?>
                                </td>
                                <td>
                                    <?php echo $row['component'] ?>
                                </td>
                                <td>
                                    <?php echo $row['algorithm'] ?>
                                </td>
                                <td value="<?php echo $row['input'] ?>"><button type="button" data-bs-toggle="modal"
                                        data-bs-target="#input-modal-<?php echo $row['id'] ?>"
                                        class="btn btn-primary">View</button></td>
                                <td value="<?php echo $row['output'] ?>"><button type="button" data-bs-toggle="modal"
                                        data-bs-target="#output-modal-<?php echo $row['id'] ?>"
                                        class="btn btn-primary">View</button></td>
                                <?php
                                if ($row['restrict_view'] == 0):
                                    ?>
                                    <td value="<?php echo $row['code'] ?>"><button type="button" data-bs-toggle="modal"
                                            data-bs-target="#code-modal-<?php echo $row['id'] ?>"
                                            class="btn btn-primary">View</button></td>
                                    <?php
                                else:
                                    ?>
                                    <td value="<?php echo $row['code'] ?>"><button type="button" data-bs-toggle="modal"
                                            data-bs-target="#code-modal-<?php echo $row['id'] ?>" class="btn btn-primary"
                                            disabled>View</button></td>
                                    <?php
                                endif;
                                ?>
                                <td><button type="button" onclick="sendToEdit('<?php echo $row['id'] ?>')"
                                        class="btn btn-primary">Edit</button></td>
                                <td>
                                    <form method="POST">
                                        <input hidden name="delete-id" value=<?php echo $row['id'] ?>></input>
                                        <button type="submit" name="deleteSubmission"
                                            onclick="confirm('Are you sure you want to delete this submission?')"
                                            class="btn btn-primary">Delete</button>
                                    </form>
                                </td>
                                <!-- <form method="post" action=".\test-run.php"> -->
                                <form method="post">
                                    <td value="<?php echo $row['id'] ?>">
                                        <input hidden name="submission_id" value="<?php echo $row['id'] ?>">
                                        </input>
                                        <input hidden name="client_code" value="<?php echo $row['client_code'] ?>">
                                        </input>
                                        <input hidden name="user_id" value="<?php echo $_SESSION['userid'] ?>">
                                        </input>
                                        <input hidden name="input-code"
                                            value="<?php echo $absolute_path . substr($row['input'], 2) ?>">
                                        </input>
                                        <input hidden name="output-code"
                                            value="<?php echo $absolute_path . substr($row['output'], 2) ?>">
                                        </input>
                                        <input hidden name="output-code"
                                            value="<?php echo $absolute_path . substr($row['code'], 2) ?>">
                                        </input>
                                        <button name="runSubmit" type="submit" class="btn btn-primary">Run</button>
                                    </td>
                                </form>
                                <!-- <form method="post" action=".\submissions\visual\sm-<?php echo $row['client_code'] ?>\index.php">  -->
                                <form method="post">
                                    <td value="<?php echo $row['id'] ?>">
                                        <input hidden name="submission_id" value="<?php echo $row['id'] ?>">
                                        </input>
                                        <input hidden name="client_code" value="<?php echo $row['client_code'] ?>">
                                        </input>
                                        <input hidden name="user_id" value="<?php echo $_SESSION['userid'] ?>">
                                        </input>
                                        <input hidden name="input-code"
                                            value="<?php echo $absolute_path . substr($row['input'], 2) ?>">
                                        </input>
                                        <input hidden name="output-code"
                                            value="<?php echo $absolute_path . substr($row['output'], 2) ?>">
                                        </input>
                                        <button name="visualizeSubmit" type="submit" class="btn btn-primary">Visualize</button>
                                    </td>
                                </form>
                            </tr>
                            <div class="modal fade" id="input-modal-<?php echo $row['id'] ?>" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Input</h5>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea class="form-control" id="input-area" name="input-area"
                                                rows="10"><?php echo file_get_contents($row['input']) ?></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="output-modal-<?php echo $row['id'] ?>" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Output</h5>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea class="form-control" id="output-area" name="output-area"
                                                rows="10"><?php echo file_get_contents($row['output']) ?></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="code-modal-<?php echo $row['id'] ?>" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Code</h5>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea class="form-control" id="code-area-<?php echo $row['id'] ?>"
                                                name="code-area"
                                                rows="10"><?php echo file_get_contents($row['code']) ?></textarea>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                var id_name = "<?php echo "code-area-" . $row['id'] ?>";
                                var myCodeMirror = CodeMirror.fromTextArea(document.getElementById(id_name), {
                                    // lineNumbers: true,
                                    tabSize: 2,
                                    autoRefresh: true,
                                    mode: "text/x-java",
                                    theme: "darcula"
                                });
                                $(document).ready(function () {
                                    $("#code-modal-<?php echo $row['id'] ?>").on('shown.bs.modal', function () {
                                        document.getElementById('code-area-<?php echo $row['id'] ?>').parentElement.querySelector('.CodeMirror').CodeMirror.refresh();
                                    })
                                })
                            </script>
                            <?php
                        endwhile;
                    else:
                        echo "Error: " . $sql . "<br>" . mysqli_error($link);
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>


<h1>My Experiments</h1>
    <!-- user experiments table -->
    <div class="row">
        <div class="col-12">
            <table class="table table-hover table-secondary">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date/Time</th>
                        <th scope="col">Category</th>
                        <th scope="col">Component</th>
                        <th scope="col">Comparison Family</th>
                        <th scope="col">Input</th>
                        <th scope="col">Output</th>
                        <th scope="col">Code</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select distinct experiments.experiment_id as id, comparisons.algorithm as comparison, 
                    comparisons.algorithm as algorithm, comparisons.client_code as client_code, 
                    experiments.input_path as input, 
                    experiments.output_path as output, 
                    experiments.code_path as code, 
                    experiments.restrict_view as restrict_view,
                    experiments.created_at as created_at
		    from experiments
                    right join (comparisons right join mechanisms on mechanisms.component_id=comparisons.component_id) 
                    on experiments.family_id=comparisons.mechanism_id 
		     where 
                    experiments.user_id=$userID 
		     order by experiments.experiment_id;";
                    $result = mysqli_query($link, $sql);
                    if ($result) :
                        while ($row = mysqli_fetch_assoc($result)) :
                            //OLD FORMATING
                            // $filePath = "files/" . $row['filePath'];
                            // echo $filePath;
                            // $id = $row['SubID'];
                            // $owner_result = mysqli_query($link, "select Email from users where UserID = $row[Owner]");
                            // $owner = mysqli_fetch_array($owner_result)[0];
                    
                            // $mechanism_result = mysqli_query($link, "select Name from mechanisms where mechanismID = $row[Mechanism]");
                            // $mechanism = mysqli_fetch_array($mechanism_result)[0];
                            ?>
                            <tr>
                                <td>
                                    <?php echo $row['id'] ?>
                                </td>
                                <td>
                                    <?php echo isset($row['created_at']) ? $row['created_at'] : date('Y-m-d H:i:s'); ?>
                                </td>
                                <td>
                                    <?php echo $row['client_code'] ?>
                                </td>
                                <td>
                                    <?php echo $row['comparison'] ?>
                                </td>
                                <td>
                                    <?php echo $row['algorithm'] ?>
                                </td>
                                <td value="<?php echo $row['input'] ?>"><button type="button" data-bs-toggle="modal"
                                        data-bs-target="#input-modal-<?php echo $row['id'] ?>"
                                        class="btn btn-primary">View</button></td>
                                <td value="<?php echo $row['output'] ?>"><button type="button" data-bs-toggle="modal"
                                        data-bs-target="#output-modal-<?php echo $row['id'] ?>"
                                        class="btn btn-primary">View</button></td>
                                <?php
                                if ($row['restrict_view'] == 0):
                                    ?>
                                    <td value="<?php echo $row['code'] ?>"><button type="button" data-bs-toggle="modal"
                                            data-bs-target="#code-modal-<?php echo $row['id'] ?>"
                                            class="btn btn-primary">View</button></td>
                                    <?php
                                else:
                                    ?>
                                    <td value="<?php echo $row['code'] ?>"><button type="button" data-bs-toggle="modal"
                                            data-bs-target="#code-modal-<?php echo $row['id'] ?>" class="btn btn-primary"
                                            disabled>View</button></td>
                                    <?php
                                endif;
                                ?>
                                <td><button type="button" onclick="sendToExpEdit('<?php echo $row['id'] ?>')"
                                        class="btn btn-primary">Edit</button></td>
                                <td>
                                    <form method="POST">
                                        <input hidden name="exp-delete-id" value=<?php echo $row['id'] ?>></input>
                                        <button type="submit" name="expdeleteSubmission"
                                            onclick="confirm('Are you sure you want to delete this experiment?')"
                                            class="btn btn-primary">Delete</button>
                                    </form>
                                </td>
                                <!-- <form method="post" action=".\test-run.php"> -->
                                <form method="post">
                                    <td value="<?php echo $row['id'] ?>">
                                        <input hidden name="experiment_id" value="<?php echo $row['id'] ?>">
                                        </input>
                                        <input hidden name="client_code" value="<?php echo $row['client_code'] ?>">
                                        </input>
                                        <input hidden name="user_id" value="<?php echo $_SESSION['userid'] ?>">
                                        </input>
                                        <input hidden name="input-code"
                                            value="<?php echo $absolute_path . substr($row['input'], 2) ?>">
                                        </input>
                                        <input hidden name="output-code"
                                            value="<?php echo $absolute_path . substr($row['output'], 2) ?>">
                                        </input>
                                        <input hidden name="output-code"
                                            value="<?php echo $absolute_path . substr($row['code'], 2) ?>">
                                        </input>
                                        <button name="runSubmit" type="submit" class="btn btn-primary">Run</button>
                                    </td>
                                </form>
                                <!-- <form method="post" action=".\experiments\visual\sm-<?php echo $row['client_code'] ?>\index.php">  -->
                                <form method="post">
                                    <td value="<?php echo $row['id'] ?>">
                                        <input hidden name="experiment_id" value="<?php echo $row['id'] ?>">
                                        </input>
                                        <input hidden name="client_code" value="<?php echo $row['client_code'] ?>">
                                        </input>
                                        <input hidden name="user_id" value="<?php echo $_SESSION['userid'] ?>">
                                        </input>
                                        <input hidden name="input-code"
                                            value="<?php echo $absolute_path . substr($row['input'], 2) ?>">
                                        </input>
                                        <input hidden name="output-code"
                                            value="<?php echo $absolute_path . substr($row['output'], 2) ?>">
                                        </input>
                                        <button name="visualizeSubmit" type="submit" class="btn btn-primary">Visualize</button>
                                    </td>
                                </form>
                            </tr>
                            <div class="modal fade" id="input-modal-<?php echo $row['id'] ?>" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Input</h5>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea class="form-control" id="input-area" name="input-area"
                                                rows="10"><?php echo file_get_contents($row['input']) ?></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="output-modal-<?php echo $row['id'] ?>" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Output</h5>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea class="form-control" id="output-area" name="output-area"
                                                rows="10"><?php echo file_get_contents($row['output']) ?></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="code-modal-<?php echo $row['id'] ?>" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Code</h5>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea class="form-control" id="code-area-<?php echo $row['id'] ?>"
                                                name="code-area"
                                                rows="10"><?php echo file_get_contents($row['code']) ?></textarea>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                var id_name = "<?php echo "code-area-" . $row['id'] ?>";
                                var myCodeMirror = CodeMirror.fromTextArea(document.getElementById(id_name), {
                                    // lineNumbers: true,
                                    tabSize: 2,
                                    autoRefresh: true,
                                    mode: "text/x-java",
                                    theme: "darcula"
                                });
                                $(document).ready(function () {
                                    $("#code-modal-<?php echo $row['id'] ?>").on('shown.bs.modal', function () {
                                        document.getElementById('code-area-<?php echo $row['id'] ?>').parentElement.querySelector('.CodeMirror').CodeMirror.refresh();
                                    })
                                })
                            </script>
                            <?php
                        endwhile;
                    else:
                        echo "Error: " . $sql . "<br>" . mysqli_error($link);
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>


</body>


</html>