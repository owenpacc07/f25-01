<?php 
    $input=$fcfs=$sjf=$sjfp=$priority=$priorityp=$roundrobin="";
    if(isset($_POST['submit'])){
        
    }

    if(isset($_POST['load'])){
        shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/cpu-v3 Scheduler");
        $input = file_get_contents("/var/www/projects/f22-02/html/files/p3/in.dat");
        $output = file_get_contents("/var/www/projects/f22-02/html/files/p3/out.dat");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>CPU Scheduling</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" 
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        
        <!--
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        -->

        <link rel="preconnect" href="https://fonts.gstatic.com">

        <!--
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        -->

        <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <link rel="stylesheet" type="text/css" href="styles/styles.css">
        <script type="module" src="./javascript/index/main.js" defer></script>
        <script type="module" src="./javascript/index/loading_data_backend.js" defer></script>
    </head>
    <body>

        <?php include '../../templates/navbar.php'; ?> 

            <div class="d-flex align-items-center justify-content-center">
                <?php
                    $p = $_GET['page'];
                    if($p=="m-001")
                        echo '<h1 id="title">First Come First Serve (FCFS)</h1>';
                    elseif($p=="m-002")
                        echo '<h1 id="title">Shortest Job First (SJF)</h1>';
                    elseif($p=="m-003")
                        echo '<h1 id="title">Priority High to Low</h1>';
                    elseif($p=="m-004")
                        echo '<h1 id="title">Priority Low to High</h1>';
                    elseif($p=="m-005")
                        echo '<h1 id="title">Round Robin (RR)</h1>';
                    else
                        echo '<h1 id="title">CPU Scheduler</h1>';
                ?>
            </div>

            
            <!-- <div id="scheduling-method-buttons" class="d-flex align-items-center justify-content-center">
                <input class="btn btn-outline-primary" type="button" value="First Come First Serve" id="fcfs" onclick="fcfs();">
                <input class="btn btn-outline-primary" type="button" value="Shortest Job First" id="sjf" onclick="sjf();">
                <input class="btn btn-outline-primary" type="button" value="Priority High" id="prioHigh" onclick="prioHigh();">
                <input class="btn btn-outline-primary" type="button" value="Priority Low" id="prioLow" onclick="prioLow();">
                <input class="btn btn-outline-primary" type="button" value="Round Robin" id="rr" onclick="rr();">
            </div> -->

            <div id="overlay">
                <div id="text">
                    Loading output data... <br>
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                
            </div>
           

            <div id="scheduling-method-buttons" class="d-flex align-items-center justify-content-center">
                <button id="fcfs" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off">First Come First Serve</button>
                <button id="sjf" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off" >Shortest Job First</button>
                <button id="prioHigh" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off" >Priority High</button>
                <button id="prioLow" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off" >Priority Low</button>
                <button id="rr" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off" disabled>Round Robin</button>
            </div>

            <div id="content">
                <?php
                    $p = $_GET['page'];
                    $page = $p.".php";
                    if(file_exists("./".$p.".php"))
                        include($page);
                    else
                        echo "<p style=\"text-align: center; font-size: 1.2em; text-decoration: underline;\"><strong>Select an algorithm to get started!</strong></p>";
                ?>
            </div>


        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->

        
    </body>
</html>

