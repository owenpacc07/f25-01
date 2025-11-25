<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Deadlock</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
       .ab {
           color: white;
           background-color: lightskyblue;
           padding: 14px 25px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin-right: 2em;
       }

       a:hover, a:active {
            background-color: lightskyblue;
        }
    </style>
</head>

<body>
    <?php include '../navbar.php'; ?>
    <br>

    <div class="container">
    <h1 class=" d-flex align-items-center justify-content-center">Deadlock</h1>
    <hr><br>
    <div class="row">
        <div class="col-4">
            <div class="card"> 
                <img class="card-img-top" src="Image/7_02_Deadlock.jpg" style="height:400px; width:330px;">
                <div class="card-body">
                    <h5 class="card-title">Deadlock Page</h5>
                    <p class="card-text">In an operating system, a deadlock occurs when a process or thread enters a waiting state because a requested system resource is held by another waiting process, which in turn is waiting for another resource held by another waiting process.</p>
                    <a class="btn btn-primary" href="p2-m1.php">Learn more</a>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card" style="min-height:630px;"> 
                <img class="card-img-top" src="Image/Reader.png">
                <div class="card-body" style="margin-top:75px;">
                    <h5 class="card-title">Reader/Writer Problem</h5>
                    <p class="card-text">The readers-writers problem relates to an object such as a file that is shared between multiple processes. Some of these processes are readers i.e. they only want to read the data from the object and some of the processes are writers i.e. they want to write into the object.</p>
                    <a class="btn btn-primary" href="p2-m2.php">Learn more</a>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card" style="min-height:630px;"> 
                <img class="card-img-top" src="Image/s6-prodcons.jpg">
                <div class="card-body" style="margin-top:85px;">
                    <h5 class="card-title">Producer/Consumer Problem</h5>
                    <p class="card-text">There is a fixed-size buffer and a Producer process, and a Consumer process. The Producer process creates an item and adds it to the shared buffer. The Consumer process takes items out of the shared buffer and "consumes" them.</p>
                    <a class="btn btn-primary" href="p2-m3.php">Learn more</a>
                </div>
            </div>
        </div>
            </div> 
    </div>
</body>

<script>
    
</script>

</html>
