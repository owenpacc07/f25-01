<?php
session_start();

//Save the mechanismID and submissionID to SESSION variables
//----------------------------------------------------------
$_SESSION['mechanismID'] = "12";
$_SESSION['submissionID'] = "333";
$_SESSION['submissionFolder'] = "sub/333/m-12";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save textbox value to session variable
    $_SESSION['subID'] = $_POST['subID'];

    // Redirect to a2.php
    header("Location: m-011/a3.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<body>

<h2>Enter subID</h2>

<form method="POST" action="a1.php">
    <label>subID: </label>
    <input type="text" name="subID" placeholder="011" required>
    <button type="submit">Go to a2.php</button>
</form>

</body>
</html>
