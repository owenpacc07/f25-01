<?php
require_once "../config-legacy.php";
global $link;

$email = $password = $userType = $userMode = $userGroup = $modeTitle = $groupName = null;
// when post is submitted add a new user to database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // if email is not empty
    if (!empty($_POST['email'])) {
        $email = trim($_POST['email']);
    } else {
        echo "Email is blank";
    }

    // if password is not empty
    if (!empty($_POST['password'])) {
        $password = trim($_POST['password']);
    } else {
        echo "Password is blank";
    }


    // if user type is not empty
    if (!empty($_POST['usertype'])) {
        $userType = trim($_POST['usertype']);
        if ($userType == "Admin") {
            $userMode = 0;
        } else if ($userType == "Student") {
            $userMode = 1;
        } else if ($userType == "Professor") {
            $userMode = 2;
        } else {
            echo "User type is invalid";
        }
    } else {
        echo "User Type is blank";
    }

    // if user mode is not empty
    if (!empty($_POST['usermode'])) {
        $userMode = trim($_POST['usermode']);

        // if user mode is admin
        if ($userMode == "Admin") {
            $userMode = 5;
        } else if ($userMode == "Manage") {
            $userMode = 4;
        } else if ($userMode == "Research") {
            $userMode = 3;
        } else if ($userMode == "Learn") {
            $userMode = 2;
        } else if ($userMode == "Basic") {
            $userMode = 1;
        } else {
            $userMode = 0;
        }
    } else {
        echo "User Mode is blank";
    }

    // if user group is not empty
    if (!empty($_POST['usergroup'])) {
        $userGroup = trim($_POST['usergroup']);
    } else {
        echo "User Group is blank";
    }

    // check if email already exists
    $sql = "SELECT EXISTS(SELECT 1 FROM users WHERE Email = '$email')";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_row($result);
    if ($row[0] == 0) {
        // add user to database
        $sql = "INSERT INTO users (Password, Email, ModeID, GroupID, userType)
         VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $param_password, $param_email, $param_userMode, $param_userGroup, $param_userType);
            $param_email = $email;
            $param_password = $password;
            $param_userType = $userType;
            $param_userMode = $userMode;
            $param_userGroup = $userGroup;
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // send email to admin

            $to = 'chapps360@gmail.com'; // switch to $email after testing
            $subject = 'User Approval';
            $from = 'chappelm1@newpaltz.edu';
            $url = 'https://cs.newpaltz.edu/p/s24-02/v0/adminPanel/index.php';

            // set message
            $message = $email . " has requested access to the admin panel. Please visit " . $url . " to approve or deny access.";



            mail($to, $subject, $message, "From: " . $from);

            // redirect
            header("Location: ./index.php?from=manage-users");
        } else {
        }
    } else {
        echo "Email already exists";
    }
}

ini_set('display_errors', 'Off');
ini_set('error_reporting', E_ALL);
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <title>Manage Users</title>
</head>

<body>

    <div class="container">
        <h2>Users</h2>

        <script>
            $(document).ready(function() {
                $('#usersTable').DataTable();
            });
        </script>
        <table id="usersTable" class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr>
                    <th class="userCol"><abbr title="The username of the user.">UserID</abbr></th>
                    <!--th><abbr title="The username of the user.">Username</abbr></th-->
                    <th><abbr title="The password of the user.">Password</abbr></th>
                    <th><abbr title="The email of the user.">Email</abbr></th>
                    <th><abbr title="What mode they belong to.">Mode</abbr></th>
                    <th><abbr title="What permissions does the user have.">Group Name</abbr></th>
                </tr>
            </thead>
            <tbody>

                <?php
                require_once '../config-legacy.php';
                $sel_query = "Select * from users;";
                $result = mysqli_query($link, $sel_query);
                while ($row = mysqli_fetch_array($result)) {
                    $modeResult = mysqli_query($link, "SELECT * from modes WHERE ModeID = '$row[ModeID]'");
                    $modeTitle = mysqli_fetch_array($modeResult)['Title'];

                    $groupResult = mysqli_query($link, "SELECT * from groups WHERE GroupID = '$row[GroupID]'");
                    $groupName = mysqli_fetch_array($groupResult)['GroupName'];                    ?>
                    <tr>
                        <td class="userCol" align="center"><input type="text" name=userid value=<?php echo $row['UserID'] ?>></td>
                        <!--td align="center"><input type="text" name=username value=?php echo $row['Username']; ?>></td-->
                        <td align="center"><input type="text" name=password value=<?php echo $row['Password']; ?>></td>
                        <td align="center"><input type="text" name=email value=<?php echo $row['Email'] ?>></td>
                        <td align="center"><input type="text" name=mode value=<?php echo $modeTitle ?>></td>
                        <td align="center"><input type="text" name=groupid value=<?php echo $groupName ?>></td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalLong">
            Add User
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Add new user</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="newUserForm" method="POST">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email/Username</label>
                                <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label for="userType">User Type</label>
                                <select class="form-control" name="usertype" id="userType">
                                    <option>Professor</option>
                                    <option>Student</option>
                                    <option>Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="userMode">User Mode</label>
                                <select class="form-control" name="usermode" id="userMode">
                                    <option>Admin</option>
                                    <option>Manage</option>
                                    <option>Research</option>
                                    <option>Learn</option>
                                    <option>Basic</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="userGroup">User Group</label>
                                <input type="text" class="form-control" name="usergroup" id="userGroup" placeholder="User Group">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="newUserForm" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>