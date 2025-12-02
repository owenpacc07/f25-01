<?php
session_start();
?>

<!DOCTYPE html>
<html>
<body>

<h2>Value from Session</h2>

<?php
if (isset($_SESSION['subID'])) {
    echo "<p>subID is: " . htmlspecialchars($_SESSION['subID']) . "</p>";
} else {
    echo "<p>No subID found in session.</p>";
}
?>

</body>
</html>
