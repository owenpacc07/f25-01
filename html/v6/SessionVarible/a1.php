<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save textbox value to session variable
    $_SESSION['subID'] = $_POST['subID'];

    // Redirect to a2.php
    header("Location: m-011/a2.php");
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
