<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        file_put_contents('t1.dat', $_POST['text1']);
        file_put_contents('t2.dat', $_POST['text2']);
        file_put_contents('t3.dat', $_POST['text3']);
        file_put_contents('t4.dat', $_POST['text4']);
        $message = "? Files saved successfully!";
    } elseif (isset($_POST['viz'])) {
        $message = "?? VIZ button clicked!";
    } elseif (isset($_POST['run'])) {
        $message = "?? RUN button clicked!";
    }
}

$text1 = file_exists('t1.dat') ? file_get_contents('t1.dat') : '';
$text2 = file_exists('t2.dat') ? file_get_contents('t2.dat') : '';
$text3 = file_exists('t3.dat') ? file_get_contents('t3.dat') : '';
$text4 = file_exists('t4.dat') ? file_get_contents('t4.dat') : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>SUBMISSION #ID</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #343a40;
        }

        textarea {
            width: 100%;
            height: 120px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 14px;
            resize: vertical;
            background-color: #fdfdfd;
        }

        label {
            font-weight: bold;
            color: #495057;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        button {
            padding: 12px 24px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button[name="save"] {
            background-color: #28a745;
            color: white;
        }

        button[name="save"]:hover {
            background-color: #218838;
        }

        button[name="viz"] {
            background-color: #17a2b8;
            color: white;
        }

        button[name="viz"]:hover {
            background-color: #138496;
        }

        button[name="run"] {
            background-color: #ffc107;
            color: #212529;
        }

        button[name="run"]:hover {
            background-color: #e0a800;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>SUBMISSION ID</h2>
        <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
        <form method="post">
            <label>INPUT (t1.dat):</label>
            <textarea name="text1"><?= htmlspecialchars($text1) ?></textarea>

            <label>OUTPUT (t2.dat):</label>
            <textarea name="text2"><?= htmlspecialchars($text2) ?></textarea>

            <label>FORMAT (t3.dat):</label>
            <textarea name="text3"><?= htmlspecialchars($text3) ?></textarea>

            <label>CODES (t4.dat):</label>
            <textarea name="text4"><?= htmlspecialchars($text4) ?></textarea>


            <div class="buttons">
                <button type="submit" name="save">SAVE data</button>
                <button type="submit" name="viz">VISUALIZE</button>
                <button type="submit" name="run">RUN</button>
                <button type="submit" name="save">CANCEL</button>
            </div>
        </form>
    </div>
</body>
</html>
