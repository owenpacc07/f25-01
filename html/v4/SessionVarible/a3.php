<?php
session_start();

// Load contents of a.txt
$filename = "a.txt";
$fileContent = "";

if (file_exists($filename)) {
    $fileContent = file_get_contents($filename);
}
?>

<!DOCTYPE html>
<html>
<body>

<h2>Value from Session</h2>

<?php
if (isset($_SESSION['subID'])) {
    echo "<p>subID is: " . htmlspecialchars($_SESSION['subID']) . "</p>";
    echo "<p>submissionID is: " . htmlspecialchars($_SESSION['submissionID']) . "</p>";
    echo "<p>mechanismID is: " . htmlspecialchars($_SESSION['mechanismID']) . "</p>";
    echo "<p>submissionFolder is: " . htmlspecialchars($_SESSION['submissionFolder']) . "</p>";
} else {
    echo "<p>No subID found in session.</p>";
}
?>

<h2>Contents of a.txt</h2>

<textarea rows="5" cols="40"><?php echo htmlspecialchars($fileContent); ?></textarea>

</body>
</html>
