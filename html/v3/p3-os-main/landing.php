<?php
session_start();

//$_SESSION['userid'];
$_SESSION['mechanismid'] = 1;

// header("Location: ../view.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['userid'] = trim($_POST['userid']);
    $_SESSION['mechanismid'] = trim($_POST['mechanismid']);
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Page</title>
</head>

<body>
    Current User ID: <?php echo $_SESSION['userid']; ?>
    <br>
    Current Mechanism ID: <?php echo $_SESSION['mechanismid']; ?>
    <br>

    <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        New User ID: <input type="text" name="userid" id="userid">
        <br>
        New Mechanism ID: <input type="text" name="mechanismid" id="mechanismid">

        <br>
        <br>

        <input type="submit" value="Submit">

    </form>

    <script>
        
    </script>



</body>

</html>