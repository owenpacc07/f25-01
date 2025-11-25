<?php
session_start();

//1st way to read/load contents of a file to the textbox
//$filename1 = "a.txt";
$filename1 = "../../../files/core-s/m-011/in-011.dat";
$fileContent1 = "";

if (file_exists($filename1)) {
    $fileContent1 = file_get_contents($filename1);
}

// Default textbox contents
$textboxContent = "";

// If the "Load Data" button was pressed
if (isset($_POST['loadData'])) {
    $filename = "a.txt";

    if (file_exists($filename)) {
        $textboxContent = file_get_contents($filename);
    }
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
    echo "<p>mechanism CODE is: " . htmlspecialchars($_SESSION['mechanismCode']) . "</p>";
    echo "<p>mechanism Title is: " . htmlspecialchars($_SESSION['mechanismTitle']) . "</p>";
    echo "<p>submissionFolder is: " . htmlspecialchars($_SESSION['submissionFolder']) . "</p>";
} else {
    echo "<p>No subID found in session.</p>";
}
?>

<h2>Contents of a.txt</h2>

<textarea rows="5" cols="40"><?php echo htmlspecialchars($fileContent1); ?></textarea>

<h2>Data Viewer</h2>

<form method="POST" action="a3.php">
    <textarea name="dataBox" rows="6" cols="50"><?php
        echo htmlspecialchars($textboxContent);
    ?></textarea>
    <br><br>

    <button type="submit" name="loadData">Load Data</button>
</form>

</body>
</html>
