<?php

// TEMPLATE

require_once "../../config-legacy.php";
global $link;
session_start();

$mid = '111'; // <-- PUT your mechanism ID HERE
$mtitle = '(Your Mechanism Title)';

// get mechanism title based on mid
/* $sql = "SELECT algorithm FROM mechanisms WHERE client_code = {$mid}";
   $result = mysqli_query($link, $sql);
   $row = mysqli_fetch_assoc($result);
   $mtitle = $row['algorithm']; */
// UNCOMMENT THIS ^^^

// run java code 
shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-{$mid} m{$mid}");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>VIZUALIZATION PAGE Template</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- allows mid to be accessible to all js files -->
    <script>
        var mid = `<?php echo $mid; ?>`;
    </script>
    <script type="module" src="main.js" defer></script>
</head>

<body>

    <?php include '../../navbar.php'; ?>

    <div id="title-content" class="d-flex align-items-center justify-content-center">
        <h1 id="title"><?= $mtitle ?></h1>
    </div>

    <div id="overlay">
        <div id="text">
            Loading output data... <br>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <!-- displays contents of input/output files. remove this eventually -->
    <div id="ioData" style="background-color: lightgray; width: 50%; margin-inline: auto;">
        <h4>Data read in from input/output files:</h4>
    </div>
    <div style="border: 1px solid black; width: 80vw; height: 70vh; margin:auto; text-align: center">
        <h1>(Visualization goes here)</h1>
    </div>
</body>

</html>