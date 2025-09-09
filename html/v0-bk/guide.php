<?php

$corename = 'View';
$coremode;

include(__DIR__ . '/system.php');

session_start();
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <title>WebVis OS Guide</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= $SITE_ROOT . $version_path ?>/pic/logocircle02.png" type="image/x-icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>

    </style>

</head>

<body>

    <?php include './navbar.php'; ?>

    

    <div class="container-fluid bg-light py-5">
        <div class="container">
            <h1 class="display-4 text-center">Welcome to the OS Visuals Users Guide</h1>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-12">
                <h2>How to use OS Visuals:</h2>
                <p>OS Visuals is a site designed to allow users to visualize some of the algorithms that are utilized by our computers every day. From cpu process scheduling algorithms to disk searching algorithms, we have a wide variety of algorithms that you can visualize and interact with. These visualizations provide a way to see how these algorithms work, and how they can be used. <br>
                    For each algorithm, there are buttons that will allow you to view the algorithm in action. For all of the algorithms, you can either step through the algorithm, or you can run the view the algorithm run straight through.
                </p>
            </div>
        </div>

    </div>

    <br><br><br><br>


    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h2>View Mode</h2>
                <p>This mode allows for any visitor of the site to view and play any of the animations on our site. This is the default mode that all users start in, and this mode uses the default data that we have provided to run the simulations. A registered account is needed to go use the site in a more interactive way, and to change the simulation data.</p>
            </div>
            <div class="col-md-6">
                <h2>Advanced Mode</h2>
                <p>Advanced mode requires logging in to use and allows you to submit your own data to have visuallized. This requires an account to use, and provides a more rich and interactive experience.</p>
            </div>
        </div>
    </div>
</body>


</html>