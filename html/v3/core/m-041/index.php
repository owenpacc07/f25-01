<!DOCTYPE html>
<html lang="en">
<?php
/*
            Contributor Spring 2023 - Dakota Marino
        */
include '../../system.php';
$mid = '041';

shell_exec("java -classpath {$cgibin_core}/m-{$mid} m{$mid}");
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Disk Scheduling</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        var mid = `<?php echo $mid; ?>`;
        var httpcore_IO = `<?php echo $httpcore_IO ?>`;
        var httpcore = `<?php echo $httpcore; ?>`;
    </script>
    <script type="module" src="main.js?v=2" defer></script>
</head>

<body>
    <?php include '../../navbar.php'; ?>

    <br />
    <div class="center text-center">
        <h1 id="title">Disk Scheduling Algorithm - First-Come, First-Served</h1>
        <h4 class="text-center">Queue and Requests</h4>
        <div class="grid-container border">
            <div class="item1">
                <p>Queue: { <span class="data" id="the-queue">Queue</span>}</p>
            </div>
            <div class="item2">
                <p>Head: <span class="data" id="the-head">Head</span></p>
            </div>
            <div class="item3">
                <p>Output Data: { <span class="data" id="output-data"></span> }</p>
            </div>
        </div>
        <br>
        <div class="">
            <div class="container">
                <br>
                <div id="animNavBtns" class="d-flex flex-row justify-content-center">
                    <a class="btn btn-primary" href="<?php echo $httpcore; ?>/m-<?php echo $mid; ?>/"> Reload Data </a>
                    <button class="btn btn-primary" type="button" id="Reset">Reset </button>
                    <button class="btn btn-primary" type="button" id="Previous">
                        < Step</button>
                            <button class="btn btn-primary" type="button" id="play">Play</button>
                            <button class="btn btn-primary" type="button" id="Next">Step > </button>
                            <button class="btn btn-primary" type="button" id="End">End </button>
                </div>
                <br>
                
                <canvas id="myCanvas" width="700" height="500">
                    Your browser does not support the HTML5 canvas tag.
                </canvas>
                
            </div>
        </div>
    </div>
    <!-- Load Chatbot Assistant -->
    <?php
    // Include chatbot loader
    $version_path = "/v3";
    $SITE_ROOT = isset($SITE_ROOT) ? $SITE_ROOT : "/p/f25-01";
    include __DIR__ . "/../../chatbot/chatbot-loader.php";
    ?>
</body>

</html>
