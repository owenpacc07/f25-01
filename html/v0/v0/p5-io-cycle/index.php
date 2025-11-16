<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>I/O Cycle</title>
    <meta name="description" content="">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.gstatic.com"> 
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> 
    <link rel="stylesheet" href="./iocycle.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src='iocycle.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>


<body>

    <?php include '../navbar.php';?>
 
    <br>

    <div class="d-flex align-items-center justify-content-center">
        <h1 id="title">I/O Cycle</h1>
        <br>

    </div>

    <br>

    <div class="text-center align-items-center justify-content-center">
        <button type="button" class="btn btn-primary" id="reset">Reset</button>
        <button type="button" class="btn btn-primary" id="play">Play</button>
        <button type="button" class="btn btn-primary" id="pause">Pause</button>
        <button type="button" class="btn btn-primary" id="next">Next</button>
        <button type="button" class="btn btn-primary" id="back">Back</button>

    </div>


    <div id="animarea">

        <?php
        $myfile = fopen("data.txt", "r") or die("Unable to open file");
        ?>

        <div id="cpu">
            <div id="title1">
                <h2> CPU </h2>
            </div>

            <div id="io-request-box2">
                <h6> I/O Request </h6>
            </div>
        </div>



        <div id="ram">
            <div id="title2">
                <h2> RAM </h2>
            </div>

            <div id="system-call-box">
                <p> System Call </p>
            </div>

            <div id="device-driver-box">
                <p> Device Driver: Monitor </p>
            </div>

            <div id="user-program-box">
                <p> User Program X</p>
            </div>

            <div id="io-request-box1">
                <h6> I/O Request </h6>
                <?php echo fread($myfile, filesize("data.txt")); ?>
            </div>

            <div id="interrupt-handler-box"> <!-- needs to take info from txt file -->
                <h6> Interrupt Handler </h6>
            </div>
        </div>

        <div id="box">
            I/O Process
        </div>

        <div id="monitor" style="display:none">
            <div id="title3">
                <h2> Monitor </h2>
            </div>
            <div id="monitor-box-display">
                <p> Display: </p>
            </div>
            <div id="monitor-box-done">
                <p> Done? </p>
            </div>
            <div id="image">
                <img src="monitor.PNG" alt="Monitor" width="200" height="150">
            </div>
            <div id="monitor-box">

            </div>
        </div>

        <div id="arrow1" style="display:none">
            <img src="arrow1.png" alt="Arrow1" width="200" height="200">
        </div>

        <div id="arrow2" style="display:none">
            <img src="arrow2.png" alt="Arrow2" width="200" height="200">
        </div>

        <div id="arrow3" style="display:none">
            <img src="arrow3.png" alt="Arrow3" width="150" height="150">
        </div>

        <div id="arrow4" style="display:none">
            <img src="arrow4copy.png" alt="Arrow4" width="350" height="150">
        </div>

        <div id="arrow5" style="display:none">
            <img src="arrow5.png" alt="Arrow5" width="150" height="100">
        </div>

        <div id="arrow6" style="display:none">
            <img src="arrow6.png" alt="Arrow6" width="350" height="350">
        </div>

        <div id="arrow7" style="display:none">
            <img src="arrow7new.png" alt="Arrow7" width="150" height="300">
        </div>

        <div id="arrow8" style="display:none">
            <img src="arrow8.png" alt="Arrow8" width="175" height="100">
        </div>

        <div id="arrow9" style="display:none">
            <img src="arrow9new.png" alt="Arrow9" width="100" height="410">
        </div>

        <div id="arrow10" style="display:none">
            <img src="arrow10.png" alt="Arrow10" width="105" height="105">
        </div>

        <div id="arrow11" style="display:none">
            <img src="arrow11.png" alt="Arrow11" width="105" height="105">
        </div>

        <div id="arrow12" style="display:none">
            <img src="arrow12.png" alt="Arrow12" width="105" height="105">
        </div>
    </div>
 
</body>

</html>
