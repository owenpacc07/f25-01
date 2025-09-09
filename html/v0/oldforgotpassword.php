<?php

require_once "config-legacy.php";

// function to find user with the given username
// then patch their current password with the new password
// and return true if successful

$newPassword = $newPasswordConfirm = $email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // set the values
    $newPassword = trim($_POST['newPassword']);
    $newPasswordConfirm = trim($_POST['newPasswordConfirm']);
    //$username = trim($_POST['username']);
    $email = trim($_POST['email']);



    global $link;
    // make sure the new passwords match
    if ($newPassword != $newPasswordConfirm) {
        echo "Passwords do not match";
    }
    // check to make sure the new password is not the same as the old password
    //else if ($newPassword == $newPasswordConfirm) {
    //    echo "New password is the same as the old password";
    //}
    // check to make sure the new password is not blank
    else if ($newPassword == "") {
        echo "New password is blank";
    }
    // check to make sure the username is not blank
    else if ($email == "") {
        echo "Email is blank";
    }
    // check if username field exists in database and if so, update the password
    else {
        $sql = "SELECT * FROM users WHERE Email = '$email'";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) > 0) {
            $sql = "UPDATE users SET password = '$newPassword' WHERE Email = '$email'";
            if (mysqli_query($link, $sql)) {
                echo "Password updated successfully";
            } else {
                echo "Error updating password: " . mysqli_error($link);
            }
        } else {
            echo "Email not found";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="./styles.css">
    <title>Password reset</title>
</head>

<body>
    <?php include './navbar.php'; ?>

    <div class="card" id="new-password">
        <div class="card-content">
            <form action="create-new-password.php" method="POST">

                <div class="field">
                    <label class="label">Email</label>
                    <div class="control">
                        <input class="input" type="email" name="email">
                    </div>
                </div>
                <div class="field">
                    <label class="label">New Password</label>
                    <div class="control">
                        <input class="input" type="password" name="newPassword">
                    </div>
                </div>

                <div class="field">
                    <label class="label">Confirm Password</label>
                    <div class="control">
                        <input class="input" type="password" name="newPasswordConfirm">
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>