<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Users</title>
</head>

<body>
    <div class="card">
        <div class="card-content">
            <h2>Delete Users</h2>

            <form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                <div class="field">
                    <label class="label">Select User to Delete</label>
                    <div class="control">
                        <select class="input" name="userid" required>
                            <option value="">-- Select User --</option>
                            <?php
                            require_once '../config-legacy.php';

                            // Fetch users from the database
                            $sel_query = "SELECT UserID, Email FROM users1;";
                            $result = mysqli_query($link, $sel_query);

                            while ($row = mysqli_fetch_array($result)) {
                                echo "<option value='" . $row['UserID'] . "'>" . $row['Email'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="control">
                    <button type="submit" class="button is-danger">Delete User</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>