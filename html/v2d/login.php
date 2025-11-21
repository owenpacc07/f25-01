<?php
session_start();

// Include config file
require_once "config-legacy.php";
global $link;

$error_message = ""; // Initialize an error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $link->escape_string($_POST['email']);
    $password = $link->escape_string($_POST['MyPass']);

    $result = $link->query("SELECT * FROM users WHERE email='$email'");

    if ($result->num_rows == 0) {
        $error_message = 'User with this email does not exist';
    } else {
        $row = $result->fetch_array();
        $stored_password = $row['Password']; // This should be the plain-text password
	//$hash = $row['Password']; // hashed password

        //verify password
	
	// Removed hash password so that admin can see passwords of users,
	// And so users can login with their password
        //if (password_verify($password, $hash)) {

	// Directly compare the passwords
        if ($password == $stored_password) {
            //had to add in this line because it was not assigning all of the session variables, only email.
            $user = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM users WHERE email = '$email'"));
            $_SESSION['logged_in'] = time();
	    $_SESSION['user_type'] = $row['userType'];
            $_SESSION["mode"] = $user['ModeID'];
            $_SESSION["userid"] = $user['UserID'];
            $_SESSION["groupid"] = $user['GroupID'];
            $_SESSION["email"] = $email;
           
	    echo ($_SESSION["userid"]);
            // redirect to home page
            header("location: index-a.php");
            exit;
        } else {
            echo '<script type="text/javascript"> 
            alert("Incorrect password")
            </script>';
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css">
     
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Login</title>
</head>

<body>
    <?php include './navbar.php'; ?>

    <div class="section container p-3 my-3">
        <div class="card">
            <header class="card-header">
                <h3 class="card-header-title">
                    Login
                </h3>
            </header>
            <div class="card-body">
                <?php
                if (isset($_GET["newpwd"])) {
                    if ($_GET["newpwd"] == "passwordupdated") {
                        echo '<p class="signupsuccess"><strong>Password reset! Log in with your new password.</strong></p>';
                    }
                }
                ?>

                <div id="error-message">
                    <?php
                    if (!empty($error_message)) {
                        echo '<p class="error">' . $error_message . '</p>';
                    }
                    ?>
                </div>

                <form method="post" action="login.php">
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input" type="email" name="email" placeholder="Email" value="">
                            <span class="icon is-small is-left">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <span class="icon is-small is-right">
                                <i class="fas fa-check" id="check"></i>
                            </span>
                            <div class="err-div"></div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input" type="password" name="MyPass" placeholder="Password">
                            <span class="icon is-small is-left">
                                <i class="fas fa-lock"></i>
                            </span>
                            <span class="icon is-small is-right">
                                <i class="fas fa-check"></i>
                            </span>
                            <div class="err-div"></div>
                            </p>
                        </div>
                    </div>
                    <div class="field is-grouped">
                        <div class="control">
                            <button class="button is-link" type="submit" name="submit">Log In</button>
                            <a class="button is-link is-light" href="indexVIEW.php">Cancel</a>
                        </div>
                    </div>
                    <hr>
                    <a href="./register.php">Need to Register?</a>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
