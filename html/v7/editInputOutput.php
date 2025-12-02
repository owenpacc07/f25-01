<?php

if (isset($_POST['submit'])) {
    if (empty($_POST['input'])) {
    } else {
        $filename = '../files/users/user' . $_SESSION['userid'] . '/mechanism' . $_SESSION['mechanismid'] . '/user' . $_SESSION['userid'] . '-mechanism' . $_SESSION['mechanismid'] . '-input.txt';
        $newData = $_POST['input'];
        file_put_contents($filename, $newData);
    }
    if (empty($_POST['output'])) {
    } else {
        $filename = '../files/users/user' . $_SESSION['userid'] . '/mechanism' . $_SESSION['mechanismid'] . '/user' . $_SESSION['userid'] . '-mechanism' . $_SESSION['mechanismid'] . '-output.txt';
        $newData = $_POST['output'];
        file_put_contents($filename, $newData);
    }

    header('Location: ../view.php');
}

?>


<html>

<head>
     
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>View Files</title>
    <style>
        body {
            background-color: #F0FFF0;
        }
    </style>
</head>

<body>

    <?php
    //include '/var/www/p/f22-02/html/templates/navbar.php'; 
    include 'templates/navbar.php';
    $input = file_get_contents('../files/users/user' . $_SESSION['userid'] . '/mechanism' . $_SESSION['mechanismid'] . '/user' . $_SESSION['userid'] . '-mechanism' . $_SESSION['mechanismid'] . '-input.txt');
    $output = file_get_contents('../files/users/user' . $_SESSION['userid'] . '/mechanism' . $_SESSION['mechanismid'] . '/user' . $_SESSION['userid'] . '-mechanism' . $_SESSION['mechanismid'] . '-output.txt');
    ?>

    <div class="section" style="display: inline;">
        <div>
            <h1 class="title is-1 has-text-centered">
                <?php
                require_once 'config-legacy.php';
                $mechanismID = $_SESSION['mechanismid'];
                $name = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM mechanisms WHERE mechanismID = '$mechanismID'"))['Name'];
                echo 'Edit Input/Output Files for ' . $name;
                ?>
            </h1>
            <form action="editInputOutput.php" method="POST">
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="input"><span><strong>Input: </strong></span></label>
                        <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output"><span><strong>Output: </strong></span></label>
                        <textarea name="output" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($fcfs); ?></textarea>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit" name="submit">Submit</button>
                        <a class="button is-link is-light" href="">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

</html>