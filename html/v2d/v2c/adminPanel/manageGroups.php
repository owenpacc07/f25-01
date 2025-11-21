<?php
require_once '../config-legacy.php';
global $link;
// define variables and set to empty values
$groupName = $managerName = $groupID = null;
$group = $manager = $userEmail = $groupid = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['userEmail'])) {
        $userEmail = trim($_POST['userEmail']);
        $groupid = trim($_POST['groupid']);
        // get the user id of the user with the email
        $sql = "SELECT UserID FROM users WHERE email = '$userEmail'";
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($result);
        $userid = $row['UserID'];
        // set the group id of the user to the group id
        $sql = "UPDATE users SET GroupID = '$groupid' WHERE UserID = '$userid'";
        $result = mysqli_query($link, $sql);


        // the the userid as the group manager
        $sql = "UPDATE groups SET Manager = '$userid' WHERE GroupID = '$groupid' ";
        $result = mysqli_query($link, $sql);
    }

    // Handle deleting a group
    if (isset($_POST['deleteGroupID'])) {
        $deleteGroupID = intval($_POST['deleteGroupID']);
        $deleteQuery = "DELETE FROM groups WHERE GroupID = '$deleteGroupID'";
        $deleteResult = mysqli_query($link, $deleteQuery);

        if (!$deleteResult) {
            echo "Error deleting group: " . mysqli_error($link);
        } else {
            // Redirect or output success message
            header("Location: index.php");
            exit();
        }
    }
}

if (isset($_POST['group'])) {
    // set the variables
    //$groupID = trim($_POST["groupid"]);
    $groupName = trim($_POST["group"]);
    $managerEmail = trim($_POST["manager"]);

    // check if the variables are empty
    if (empty($groupName)) {
        $group = "Group name is required";
    }
    if (empty($managerEmail)) {
        $manager = "Manager email is required";
    }
    // if the variables are not empty add them to the Groups table
    if (!empty($groupName) && !empty($managerEmail)) {
        // Get the UserID for the manager based on the email
        $sql = "SELECT UserID FROM users WHERE Email = '$managerEmail'";
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($result);
        $managerID = $row['UserID'];

        // Check if the UserID was found
        if ($managerID) {
            $ins_query = "INSERT INTO groups (GroupName, Manager) VALUES ('$groupName', '$managerID')";
            $result = mysqli_query($link, $ins_query);
            if ($result) {
                if ($result) {
                    echo "Group added successfully";
                    // Redirect to index.php after adding group
                    header("Location: index.php");
                } else {
                    echo "Error: " . $ins_query . "<br>" . mysqli_error($link);
                }
            } else {
                echo "Error: Manager not found with the provided email.";
            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


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

</head>

<body>
    <script>
        $(document).ready(function () {
            $('#groupsT').DataTable();
        });
    </script>
    <table class="table is-hoverable is-striped" id="groupsT">
        <thead>
            <tr>
                <th>Group ID</th>
                <th>Group Name</th>
                <th>Manager</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM groups";

            $manager;
            $result = mysqli_query($link, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $groupID = $row['GroupID'];
                if ($row['Manager'] == 0) {
                    $manager = "<form id=\"formFor$groupID\" method=\"POST\">
                    <div class=\"input-group mb-3\">
                        <input type=\"email\" name=\"userEmail\" class=\"form-control\" placeholder=\"Users's email\" aria-label=\"Users's email\" aria-describedby=\"basic-addon2\">
                        <input type=\"hidden\" name=\"groupid\" value=\"$groupID\">
                        <div class=\"input-group-append\">
                        <button class=\"btn btn-outline-secondary\" type=\"submit\">Set</button>
                        </div>
                  </div></form>";
                } else {
                    $sel_query = "Select Email from users where UserID = '$row[Manager]';";
                    $result2 = mysqli_query($link, $sel_query);
                    $row2 = mysqli_fetch_array($result2);

                    $manager = $row2['Email'];
                }
                echo "<tr>";
                echo "<td>" . $row['GroupID'] . "</td>";
                echo "<td>" . $row['GroupName'] . "</td>";
                echo "<td>" . $manager . "</td>";
                echo "<td>";

                echo "<button type=\"button\" class=\"btn btn-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#group$groupID\">
                    Members
                  </button>";


                // Delete Group Button
                echo "<form method=\"POST\" action=\"manageGroups.php\" style=\"display:inline;\">
        <input type=\"hidden\" name=\"deleteGroupID\" value=\"$groupID\">
        <button type=\"submit\" class=\"btn btn-danger btn-sm\" style=\"padding: 7.5px 20px;\" onclick=\"return confirm('Are you sure you want to delete this group?');\">Delete</button>
      </form>";


                echo "<!-- Modal -->
                  <div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\" id=\"group$groupID\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                      <div class=\"modal-content\">
                        <div class=\"modal-header\">
                          <h5 class=\"modal-title\" id=\"exampleModalLabel\">Members</h5>
                          <button type=\"button\" class=\"btn close\" data-bs-dismiss=\"modal\" aria-label=\"Close\">
                            <span aria-hidden=\"true\">&times;</span>
                          </button>
                        </div>
                        <div class=\"modal-body\">";
                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Email</th>
                            <th scope="col">Stuff</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sel_query = "SELECT * from users WHERE GroupID = '$groupID';";
                        $result2 = mysqli_query($link, $sel_query);

                        while ($row_2 = mysqli_fetch_array($result2)) {
                            echo "<tr>";
                            echo "<td>" . $row_2['UserID'] . "</td>";
                            echo "<td>" . $row_2['Email'] . "</td>";
                            echo "<td>...</td>";

                            echo "</tr>";
                        }

                        ?>
                    </tbody>
                </table>
                <?php echo "</div>
                        <div class=\"modal-footer\">
                        <a class=\"btn btn-primary\" href=\"./groupPage.php?id=$groupID\" role=\"button\">Group Page</a>
                          <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>";
            }
            ?>
            </td>
            </tr>

        </tbody>
    </table>

    <!-- Button to trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGroup">
        Add New Group
    </button>

    <!-- Modal -->
    <div class="modal fade" id="modalGroup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Group</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="index.php" id="addGroupForm">
                        <div class="form-group">
                            <label for="newGroupName">Group Name:</label>
                            <input type="text" name="group" id="newGroupName">
                        </div>
                        <div class="form-group">
                            <label for="newGroupManager">Group Manager Email:</label>
                            <input type="text" name="manager" id="newGroupManager">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" form="addGroupForm" class="btn btn-primary" value="Add Group"></button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>