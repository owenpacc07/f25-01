<?php
require_once "../config-legacy.php";
global $link;

// REMOVE THIS IF YOU ARE TESTING
// ALSO LOOK THROUGH THE ECHO WARNINGS BELOW THAT ARE COMMENTED OUT
// Disable warnings
error_reporting(E_ERROR | E_PARSE);  // Only fatal errors and parse errors are reported
ini_set('display_errors', '0');  // Suppress display of errors

//to add a new user to the database upon form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //initialize variables
    $userID = $email = $password = $userType = $userMode = $userGroup = null;

    // Check if userID is provided and valid
    if (!empty($_POST['userID']) && is_numeric($_POST['userID'])) {
        $userID = $_POST['userID'];

        // Check if UserID is unique
        $checkUserIDQuery = "SELECT COUNT(*) as count FROM users WHERE UserID = '$userID'";
        $checkResult = mysqli_query($link, $checkUserIDQuery);
        $count = mysqli_fetch_assoc($checkResult)['count'];

        if ($count > 0) {
            echo "User ID already exists. Please choose a different ID.";
        }
    } else {
        //echo "User ID is blank or invalid  |    ";
    }

    // if email is not empty
    if (!empty($_POST['email'])) {
        $email = $_POST['email'];
    } else {
        //echo "Email is blank  |    ";
    }

    //checks if input is empty
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        //echo "Password is blank  |    ";
    }

    //checks if input is empty
    if (!empty($_POST['userType'])) {
        $userType = $_POST['userType'];
        //if not empty, checks input
        if ($userType == "Admin") {
            $userType = 1;
        } else if ($userType == "Student") {
            $userType = 0;
        } else if ($userType == "Professor") {
            $userType = 0;
        } else {
            echo "User type is invalid";
        }
    } else {
        //echo "User Type is blank  |    ";
    }

    //if userMode is not empty
    if (!empty($_POST['userMode'])) {
        $userMode = $_POST['userMode'];
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
        //echo "User Mode is blank  |    ";
    }

    // if user group is not empty
    if (!empty($_POST['userGroup'])) {
        $userGroup = $_POST['userGroup'];
    } else {
        //echo "User Group is blank  |    ";
    }


    $sql = "INSERT INTO users (UserID, Password, Email, ModeID, GroupID, userType)
        VALUES ('$userID', '$password', '$email', '$userMode', '$userGroup', '$userType')";


    $result = mysqli_query($link, $sql);
    if (!$result) {
        // Handle query error
        //echo "Error: " . mysqli_error($link);
    }
} else {
    // Handle case where some fields are missing
    //echo "Please Fill in All Required Fields!";
}

if (isset($_POST['deleteUserID'])) {
    $deleteUserID = $_POST['deleteUserID'];
    $deleteQuery = "DELETE FROM users WHERE UserID = '$deleteUserID'";
    $deleteResult = mysqli_query($link, $deleteQuery);

    if (!$deleteResult) {
        echo "Error deleting user: " . mysqli_error($link);
    }
}

// Fetch user groups from the database
$groupQuery = "SELECT GroupID, GroupName FROM groups";
$groupResult = mysqli_query($link, $groupQuery);
$groups = [];
while ($row = mysqli_fetch_assoc($groupResult)) {
    $groups[] = $row;
}




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <title>Manage Users</title>
</head>

<body>

    <div class="container">
        <h2>Users</h2>

        <script>
            $(document).ready(function () {
                $('#usersTable').DataTable({
                    "pageLength": 10
                });
            });
        </script>
        <table id="usersTable" class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr>
                    <th class="userCol"><abbr title="The userid of the user.">UserID</abbr></th>
                    <!--th><abbr title="The username of the user.">Username</abbr></th-->
                    <th><abbr title="The password of the user.">Password</abbr></th>
                    <th><abbr title="The email of the user.">Email</abbr></th>
                    <th><abbr title="What mode they belong to.">Mode</abbr></th>
                    <th><abbr title="What permissions does the user have.">Group Name</abbr></th>
                    <th>Actions</th>
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
                    $groupName = mysqli_fetch_array($groupResult)['GroupName']; ?>
                    <tr>
                        <td class="userCol" align="center">
                            <input type="text" name="userid" value="<?php echo $row['UserID']; ?>" readonly>
                        </td>
                        <td align="center"><input type="text" name="password" value="<?php echo $row['Password']; ?>"
                                readonly></td>
                        <td align="center"><input type="text" name="email" value="<?php echo $row['Email']; ?>" readonly>
                        </td>
                        <td align="center"><input type="text" name="mode" value="<?php echo $modeTitle; ?>" readonly></td>
                        <td align="center"><input type="text" name="groupid" value="<?php echo $groupName; ?>" readonly>
                        </td>
                        <td align="center">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="deleteUserID" value="<?php echo $row['UserID']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            </form>
                    </tr>

                <?php } ?>
            </tbody>
        </table>
        <!-- button to trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalLong">
            Add User
        </button>


        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLongTitle" aria-hidden="true">
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
                                <label for="userID">User ID</label>
                                <input type="number" name="userID" class="form-control" placeholder="Enter User ID">
                            </div>
                            <div class="form-group">
                                <label for="email">Email/Username</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    aria-describedby="emailHelp" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label for="userType">User Type</label>
                                <select class="form-control" name="userType" id="userType">
                                    <option>Professor</option>
                                    <option>Student</option>
                                    <option>Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="userMode">User Mode</label>
                                <select class="form-control" name="userMode" id="userMode">
                                    <option>Admin</option>
                                    <option>Manage</option>
                                    <option>Research</option>
                                    <option>Learn</option>
                                    <option>Basic</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="userGroup">User Group</label>
                                <select class="form-control" name="userGroup" id="userGroup">
                                    <option value="">Select User Group</option>
                                    <?php foreach ($groups as $group): ?>
                                        <option value="<?php echo $group['GroupID']; ?>"><?php echo $group['GroupName']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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