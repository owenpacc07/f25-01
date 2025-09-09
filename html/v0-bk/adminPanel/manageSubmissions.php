<!DOCTYPE html>
<html lang="en">

<?php
$assignmentName = $dueDate = $assignmentDescription = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['assignmentName'])) {
        // set vars
        $assignmentName = $_POST['assignmentName'];
        //$assignmentID = $_POST['assignmentID'];
        $dueDate = $_POST['assignmentDueDate'];
        $assignmentDescription = $_POST['assignmentDescription'];
        $assignmentPoints = $_POST['assignmentPoints'];

        // add the new assignment to the table
        $sql = "INSERT INTO assignments (assignmentName, dueDate, assignmentDescription, assignmentPoints)
     VALUES ('$assignmentName', '$dueDate', '$assignmentDescription', '$assignmentPoints')";
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo "Error: " . $sql . "<br>" . mysqli_error($link);
        } else {
            // add a url param from=manage-submissions

            header("Location: ./index.php?from=manage-submissions");
            //exit;

        }
    }
    // Handle assignment deletion
    if (isset($_POST['deleteAssignmentID'])) {
        $deleteAssignmentID = $_POST['deleteAssignmentID'];
        $deleteQuery = "DELETE FROM assignments WHERE assignmentID = '$deleteAssignmentID'";
        $deleteResult = mysqli_query($link, $deleteQuery);

        if (!$deleteResult) {
            echo "Error deleting assignment: " . mysqli_error($link);
        } else {
            // Refresh the page after deletion
            header("Location: ./index.php?from=manage-submissions");
        }
    }
}


?>




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

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>


</head>

<body>
    <div class="container">
        <h2>Submissions</h2>

        <!-- table of submissions which has pages and 15 submissions per page -->
        <table class="table table-striped" id="subs">
            <thead>
                <tr>
                    <th>SubID</th>
                    <th>Owner</th>
                    <th>Mechanism</th>
                    <th>Input Path</th>
                    <th>Output Path</th>

                </tr>
            </thead>
            <tbody>
                <?php
                // get all the assignments from the database
                $sql = "SELECT * FROM submissions";
                $result = mysqli_query($link, $sql);
                if (!$result) {
                    echo "Error: " . $sql . "<br>" . mysqli_error($link);
                } else {
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['submission_id'] . "</td>";
                        echo "<td>" . $row['user_id'] . "</td>";
                        echo "<td>" . $row['mechanism_id'] . "</td>";
                        echo "<td>" . $row['input_path'] . "</td>";
                        echo "<td>" . $row['output_path'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <script>
            $(document).ready(function () {
                $('#subs').DataTable();
            });
        </script>

        <br>
        <hr>
        <br>

        <script>
            $(document).ready(function () {
                $('#expassi').DataTable();
            });
        </script>

        <h2>Assignments</h2>
        <!-- View assignments -->
        <table class="table table-striped is-hoverable" id="expassi">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Due</th>
                    <th scope="col">Points</th>
                    <th scope="col"># of submissions</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM assignments";
                $result = mysqli_query($link, $sql);

                while ($row = mysqli_fetch_assoc($result)) {

                    echo "<tr>";
                    echo "<td>" . $row['assignmentID'] . "</td>";
                    echo "<td>" . $row['assignmentName'] . "</td>";
                    echo "<td>" . $row['assignmentDescription'] . "</td>";
                    echo "<td>" . $row['dueDate'] . "</td>";
                    echo "<td>" . $row['assignmentPoints'] . "</td>";
                    echo "<td>1</td>";
                    // Delete button in the Actions column
                    echo "<td>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='deleteAssignmentID' value='" . $row['assignmentID'] . "'>
                        <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this assignment?\");'>Delete</button>
                    </form>
                </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <hr>
        <!-- Create assignments -->
        <div class="card">
            <div class="card-header">
                <h2>New Assignment</h2>
            </div>
            <div class="card-content">
                <form method="POST">
                    <div class="form-group">
                        <label for="assignmentName">Assignment Name</label>
                        <input type="text" class="form-control" id="assignmentName" name="assignmentName"
                            placeholder="Assignment Name">
                    </div>
                    <div class="form-group">
                        <label for="assignmentDescription">Assignment Description</label>
                        <input type="text" class="form-control" id="assignmentDescription" name="assignmentDescription"
                            placeholder="Assignment Description">
                    </div>
                    <div class="form-group">
                        <label for="assignmentDueDate">Assignment Due Date</label>
                        <input type="date" class="form-control" id="assignmentDueDate" name="assignmentDueDate"
                            placeholder="Assignment Due Date">
                    </div>
                    <div class="form-group">
                        <label for="assignmentPoints">Assignment Points</label>
                        <input type="number" class="form-control" id="assignmentPoints" name="assignmentPoints"
                            placeholder="Assignment Points">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Assignment</button>
                </form>
            </div>
        </div>

    </div>
</body>

</html>