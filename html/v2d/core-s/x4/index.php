<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        file_put_contents('t1.dat', $_POST['text1']);
        file_put_contents('t2.dat', $_POST['text2']);
        file_put_contents('t3.dat', $_POST['text3']);
        file_put_contents('t4.dat', $_POST['text4']);
        $message = "Files saved successfully!";
    } elseif (isset($_POST['viz'])) {
        $message = "VIZ button clicked!";
        // Add visualization logic here
    } elseif (isset($_POST['run'])) {
        $message = "RUN button clicked!";
        // Add execution logic here
    }
}

// Load file contents
$text1 = file_exists('t1.dat') ? file_get_contents('t1.dat') : '';
$text2 = file_exists('t2.dat') ? file_get_contents('t2.dat') : '';
$text3 = file_exists('t3.dat') ? file_get_contents('t3.dat') : '';
$text4 = file_exists('t4.dat') ? file_get_contents('t4.dat') : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editable Texts</title>
    <style>
        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }
        .buttons {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Edit Text Files</h2>
    <?php if (!empty($message)) echo "<p><strong>$message</strong></p>"; ?>
    <form method="post">
        <label>Text 1 (t1.dat):</label><br>
        <textarea name="text1"><?= htmlspecialchars($text1) ?></textarea><br>

        <label>Text 2 (t2.dat):</label><br>
        <textarea name="text2"><?= htmlspecialchars($text2) ?></textarea><br>

        <label>Text 3 (t3.dat):</label><br>
        <textarea name="text3"><?= htmlspecialchars($text3) ?></textarea><br>

        <label>Text 4 (t4.dat):</label><br>
        <textarea name="text4"><?= htmlspecialchars($text4) ?></textarea><br>

        <div class="buttons">
            <button type="submit" name="save">SAVE</button>
            <button type="submit" name="viz">VIZ</button>
            <button type="submit" name="run">RUN</button>
        </div>
    </form>
</body>
</html>
