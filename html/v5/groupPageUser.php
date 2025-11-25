<?php
require_once "./config-legacy.php";
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="./styles.css">

    <?php
    // get the group name from the groupID
    $sql = "SELECT GroupName FROM groups WHERE GroupID = '$groupID'";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $groupName = $row['GroupName'];
    echo "<title>" . $groupName . "'s page</title>";
    ?>
</head>

<body>

    <?php include './navbar.php'; ?>
    <br>


    <div class="container">
        <div class="row">
            <div class="col">
                <div class="groupPageTableStyle">
                    <label>Memebers</label>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Role</th>
                                <th scope="col">Email</th>

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
                            if ($result_1) {
                                $row_1 = mysqli_fetch_assoc($result_1);
                                echo "<tr>";
                                echo "<td>" . "Manager" . "</td>";
                                echo "<td>" . $row_1['Email'] . "</td>";
                            } else {
                                echo "<tr>";
                                echo "<td>" . "Manager" . "</td>";
                                echo "<td> Unset </td>";
                            }




                            echo "</tr>";
                            ?>


                            <tr>
                                <!-- spacer row -->
                                <th scope="row"></th>
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

                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                </div>

            </div>
            <div class="col">
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
            </div>
        </div>
    </div>








</body>

</html>