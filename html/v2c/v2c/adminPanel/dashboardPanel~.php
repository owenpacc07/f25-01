<?php
require_once('../config-legacy.php');
global $link;

$role = $user = "";
// when POST is used, the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected radio button in the form
    if (isset($_POST["role"])) {
        $role = trim($_POST["role"]);
    }
    if (isset($_POST["userID"])) {
        $user = trim($_POST["userID"]);
    }
    if (isset($role) && isset($user)) {
        // update user's ModeID with role
        $sql = "UPDATE users1 SET ModeID = ? WHERE UserID = ?";

        //$sql = "UPDATE users1 SET ModeID = ? WHERE Username = ?";
        // prepare the statement
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind the parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_modeid, $param_UserID);

            // set the parameters
            $param_modeid = $role;
            $param_UserID = $user;


            // execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // dont redirect
                header("location: ./index.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    }
    // close the statement
    mysqli_stmt_close($stmt);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVr5+8PkUETLC2Dfjj5uPZ87f73" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-qCSqRiP47qJWdZ6Vh71m8tibWWxNlpa3Ou6ph4z0W3O7J7ImdF8R+1MtO2hE8QQ"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-faLbVpoKwqALyj2gJfDTUbSKP48i3FUqDIldF9knexUt+8dEN9R/5O7bs6RS6N+"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap5.min.js"></script>
</head>

<body>
    <div class="dashboard">
        <!-- get all the users with the mode marked as needing approval or however we mark it -->

        <h1>Approvals</h1>

        <script>
            $(document).ready(function () {
                $('#approval').DataTable();

            });
        </script>
        <table class="table is-striped is-hoverable" id="approval">
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>Email</th>
                    <th>Approve</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //
                // Need to add pages to the table, 10 users per page
                //
                //require_once '../config-legacy.php';
                //global $link;
                // get all the users with the mode 0 and list their username and email in a table
                $sql = "SELECT * FROM users1 WHERE ModeID = 0";
                $result = mysqli_query($link, $sql);
                if (mysqli_num_rows($result) > 0) {


                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['UserID'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        $UserID = $row['UserID'];
                        // echo dropdown with approval options
                        ///
                        /// Need to set up the modal so on submission is changes the ModeID in database to the correct value
                        ///
                        echo "<td>
                    <div class=\"dropright\">
                        <button type=\"button\" class=\"btn btn-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#dropdownFor$UserID\">
                            Set Mode 
                        </button>
                    
                        <div class=\"modal\" tabindex=\"-1\" role=\"dialog\" id=\"dropdownFor$UserID\">
                            <div class=\"modal-dialog\" role=\"document\">
                            <div class=\"modal-content\">
                                <div class=\"modal-header\">
                                    <h5 class=\"modal-title\">$UserID Permissions</h5>
                                    <button type=\"button\" class=\"btn close\" data-bs-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                    </button>
                                </div>
                                <div class=\"modal-body\">
                            
                                    <form class=\"dynamic-form\" id=\"formFor\" action=\"dashboardPanel.php\" method=\"POST\">
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role1\" value=\"5\" checked>
                                            <label class=\"form-check-label\" for=\"role1\">
                                                Admin
                                            </label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role2\" value=\"4\" checked>
                                            <label class=\"form-check-label\" for=\"role2\">
                                                Research
                                            </label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role3\" value=\"3\" checked>
                                            <label class=\"form-check-label\" for=\"role3\">
                                                Manage
                                            </label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role4\" value=\"2\" checked>
                                            <label class=\"form-check-label\" for=\"role4\">
                                                Learn
                                            </label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role5\" value=\"1\" checked>
                                            <label class=\"form-check-label\" for=\"role5\">
                                                Basic
                                            </label>
                                        </div>

                                        <div class=\"form-check\">
                                            <input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
                                        </div>
                                    </form>

                                </div>

                                <div class=\"modal-footer\">
                                    <input type=\"submit\" form=\"formFor\" class=\"btn btn-primary\" value=\"Save Changes\"></input>
                                    <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Close</button>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<tr>
                    <td>No users to approve</td>
                    <td></td>
                    <td></td>
                    </tr>";
                }

                ?>


    </div>

</body>

</html>