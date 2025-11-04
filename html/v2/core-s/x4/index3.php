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
    <title>Tech Editor</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #0f1117;
            font-family: 'Fira Code', monospace;
            color: #e0e0e0;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #1a1d24;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,255,255,0.1);
        }

        h2 {
            text-align: center;
            color: #00f7ff;
            margin-bottom: 30px;
            font-weight: 600;
        }

        label {
            display: block;
            margin: 20px 0 5px;
            color: #9be7ff;
            font-size: 14px;
        }

        textarea {
            width: 100%;
            height: 140px;
            background: #0f1117;
            color: #e0e0e0;
            border: 1px solid #2c2f36;
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
            resize: vertical;
            box-shadow: inset 0 0 5px rgba(0,255,255,0.05);
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        button {
            flex: 1;
            margin: 0 10px;
            padding: 14px 0;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: linear-gradient(145deg, #00c6ff, #0072ff);
            color: white;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,255,255,0.2);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,255,255,0.3);
        }

        button[name="viz"] {
            background: linear-gradient(145deg, #ff6ec4, #7873f5);
        }

        button[name="run"] {
            background: linear-gradient(145deg, #00ff87, #60efff);
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            color: #00f7ff;
        }

        @media (max-width: 768px) {
            .buttons {
                flex-direction: column;
            }
            button {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>?? CodePad: Technical Text Editor</h2>
        <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
        <form method="post">
            <label>Text 1 (t1.dat):</label>
            <textarea name="text1"><?= htmlspecialchars($text1) ?></textarea>

            <label>Text 2 (t2.dat):</label>
            <textarea name="text2"><?= htmlspecialchars($text2) ?></textarea>

            <label>Text 3 (t3.dat):</label>
            <textarea name="text3"><?= htmlspecialchars($text3) ?></textarea>

            <label>Text 4 (t4.dat):</label>
            <textarea name="text4"><?= htmlspecialchars($text4) ?></textarea>

            <div class="buttons">
                <button type="submit" name="save">?? SAVE</button>
                <button type="submit" name="viz">?? VIZ</button>
                <button type="submit" name="run">?? RUN</button>
            </div>
        </form>
    </div>
</body>
</html>
