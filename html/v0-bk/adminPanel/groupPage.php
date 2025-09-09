<?php
require_once "../config-legacy.php";
global $link;



$groupID = $_GET['id'];
unset($_GET['id']);


$email = $userID = null;



// on submit update the user to have groupid = $groupID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['UserEmail'])) {
        $email = $_POST['UserEmail'];
        $sql = "UPDATE users SET groupID = '$groupID' WHERE email = '$email'";
        $result = mysqli_query($link, $sql);
        if ($result) {
        } else {
        }
    }
    if (isset($_POST['userID'])) {
        $userID = $_POST['UserID'];
        $sql = "UPDATE users SET groupID = '$groupID' WHERE userID = '$userID'";
        $result = mysqli_query($link, $sql);
        if ($result) {
        } else {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="./styles.css">

    <title>Group Page</title>
</head>

<body>

    <?php include '../navbar.php'; ?>
    <br>




    <div class="groupPageTableStyle">
        <label>Members</label>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Role</th>
                    <th scope="col">Email</th>
                    <th scope="col">Mode</th>
                    <th scope="col">Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // select manager from groups table with groupID of 7
                $sel_query = "SELECT * FROM groups WHERE GroupID = $groupID";
                $result = mysqli_query($link, $sel_query);
                $row = mysqli_fetch_assoc($result);
                $manager = $row['Manager'];

                // get managers user info and display it in the table
                $sel_query_1 = "SELECT * FROM users WHERE Email = $manager";
                $result_1 = mysqli_query($link, $sel_query_1);
                $row_1 = mysqli_fetch_assoc($result_1);
                echo "<tr>";
                echo "<td>" . "Manager" . "</td>";
                echo "<td>" . $row_1['Email'] . "</td>";
                echo "<td>" . $row_1['ModeID'] . "</td>";
      		echo "<td>" . "" . "</td";
                echo "</tr>";


                ?>


                <tr>
                    <!-- spacer row -->
                    <th scope="row"></th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                $sql = "SELECT * FROM users WHERE GroupID = '$groupID'";
                $result = mysqli_query($link, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    $em = $row['Email'];
                    echo "<tr>";
                    echo "<th scope='row'>" . $row['userType'] . "</th>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>
                    <div class=\"btn-group\" role=\"group\" aria-label=\"Button group with nested dropdown\">
                    <button type=\"button\" class=\"btn btn-primary\">1</button>
                    <button type=\"button\" class=\"btn btn-primary\">2</button>

                    <div class=\"btn-group\" role=\"group\">
                        <button id=\"btnGroupDrop1\" type=\"button\" class=\"btn btn-primary dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                        Dropdown
                        </button>
                        <ul class=\"dropdown-menu\" aria-labelledby=\"btnGroupDrop1\">
                        <li><a class=\"dropdown-item\" href=\"#\">Dropdown link</a></li>
                        <li><a class=\"dropdown-item\" href=\"#\">Dropdown link</a></li>
                        </ul>
                    </div>
                    </div>
                    </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

    </div>

    <div class="groupPageTableStyle" id="view-assignments">
        <label>Assingments</label>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Due</th>
                    <th scope="col"># of submissions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM assignments";
                $result = mysqli_query($link, $sql);

                while ($row = mysqli_fetch_assoc($result)) {


                    // check the number of submissions for each assignment



                    echo "<tr>";
                    echo "<th scope='row'>" . $row['assignmentID'] . "</th>";
                    echo "<td>" . $row['assignmentName'] . "</td>";
                    echo "<td>" . $row['dueDate'] . "</td>";
                    echo "<td>1</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <div class="groupPageTableStyle" id="add-members">
        <label>Add Members</label>
        <!-- 2 tabs one for adding member by id and one for seeing a table of all unset users -->
        <div class="tabs is-centered is-boxed">
            <ul>
                <li>
                    <a onclick="toggleTabs()">

                        <span>By ID</span>
                    </a>
                </li>
                <li>
                    <a onclick="toggleTabs()">

                        <span>Table</span>
                    </a>
                </li>

            </ul>
        </div>
        <div class="tabs-content">
            <div id="byid" style="display: block;">
                <form method="POST">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="UserEmail" placeholder="Users's email" aria-label="Users's email" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Add Member</button>                        </div>
                    </div>
                </form>
            </div>
            <div id="bytable" style="display: none;">
                <?php
                // make a table of all users that are not in a group
                $sql = "SELECT * FROM users WHERE GroupID = 0";
                $result = mysqli_query($link, $sql);

                echo "<table class='table table-striped'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th scope='col'>#</th>";
                echo "<th scope='col'>Email</th>";
                echo "<th scope='col'>Mode</th>";
                echo "<th scope='col'>Add</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                echo "<th scope='row'></th>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    $UserID =  $row['UserID'];
                    echo "<tr>";
                    echo "<th scope='row'>" . $UserID . "</th>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>" . $row['ModeID'] . "</td>";
                    echo "<td>" .
                        "<form method='POST'> 
                    <input type='hidden' name='UserID' value='$UserID'>
                    <input type='submit' name='add' value='Add' class='btn btn-primary'>
                    </form>"
                        . "</td>";
                    echo "</tr>";
                }

                ?>
            </div>
        </div>

    </div>
    <script>
        // funtion to toggle between byid and bytable tabs
        function toggleTabs() {
            var x = document.getElementById("byid");
            var y = document.getElementById("bytable");
            if (x.style.display === "block") {
                x.style.display = "none";
                y.style.display = "block";
            } else {
                x.style.display = "block";
                y.style.display = "none";
            }
        }
    </script>

</body>

</html>