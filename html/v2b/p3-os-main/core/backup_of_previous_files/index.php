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

        <link rel="stylesheet" type="text/css" href="styles.css">
        <!--<script type="module" src="main.js" defer></script>-->
        <script type="module" src="loading_data_backend.js" defer></script>
    </head>
    <body>

            <div class="d-flex align-items-center justify-content-center">
                <h1 id="title">CPU Scheduler</h1>
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
                <button id="rr" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off">Round Robin</button>
            </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script>
            // First Come First Serve
            var fcfs = document.getElementById('fcfs');
            fcfs.addEventListener("click", fcfsClick);
            function fcfsClick(e) {
            
                location.replace("https://cs.newpaltz.edu/p/f22-02/v3/p3-os-main/core/m-001.php");
            
                schedulingMethod = "fcfs";
            
                // FCFS is only Non-Preemptive so disable radio buttons and set to Non-Preemptive
                let nonPreRadioButton = document.getElementById('non-preemptive');
                nonPreRadioButton.checked = true;
                nonPreRadioButton.disabled = false;
            
                let preRadioButton = document.getElementById('preemptive');
                preRadioButton.checked = false;
                preRadioButton.disabled = true;
            
            
                console.log("FCFS: Value 0");
                console.log("before files updated")
                toggleOverlay();
                updateSchedulerType(0);
                loadingData(0);
            
            }
            
            // Shortest Job First
            var sjf = document.getElementById('sjf');
            sjf.addEventListener("click", sjfClick);
            function sjfClick(e) {
                // Re-enable radio buttons
                if (schedulingMethod === "fcfs" || schedulingMethod === "rr") {
                    let nonPreRadioButton = document.getElementById('non-preemptive');
                    nonPreRadioButton.disabled = false;
                    let preRadioButton = document.getElementById('preemptive');
                    preRadioButton.disabled = false;
                }
            
                schedulingMethod = "sjf";
            
                toggleOverlay();
                if (preemptive) {
                    console.log("Preemptive SJF");
                    updateSchedulerType(5);
                    loadingData(5);
                } else {
                    console.log("Non-preemptive SJF");
                    updateSchedulerType(1);
                    loadingData(1);
                }
            
            }
            
            
            // Priority High
            var prioHigh = document.getElementById('prioHigh');
            prioHigh.addEventListener("click", prioHighClick);
            function prioHighClick(e) {
                // Re-enable radio buttons
                if (schedulingMethod === "fcfs" || schedulingMethod === "rr") {
                    let nonPreRadioButton = document.getElementById('non-preemptive');
                    nonPreRadioButton.disabled = false;
                
                    let preRadioButton = document.getElementById('preemptive');
                    preRadioButton.disabled = false;
                }
            
                schedulingMethod = "prioHigh"
            
                toggleOverlay();
                if (preemptive) {
                    console.log("Preemptive Priority High -> Low");
                    updateSchedulerType(6);
                    loadingData(6);
                } else {
                    console.log("Non-preemptive Priority High -> Low");
                    updateSchedulerType(2);
                    loadingData(2);
                }
            
            }
            
            
            // Priority Low
            var prioLow = document.getElementById('prioLow');
            prioLow.addEventListener("click", prioLowClick);
            function prioLowClick(e) {
                // Re-enable radio buttons
                if (schedulingMethod === "fcfs" || schedulingMethod === "rr") {
                    let nonPreRadioButton = document.getElementById('non-preemptive');
                    nonPreRadioButton.disabled = false;
                
                    let preRadioButton = document.getElementById('preemptive');
                    preRadioButton.disabled = false;
                }
            
                schedulingMethod = "prioLow"
            
                toggleOverlay();
                if (preemptive) {
                    console.log("Preemptive Priority Low -> High");
                    updateSchedulerType(7);
                    loadingData(7);
                } else {
                    console.log("Non-preemptive Priority Low -> High");
                    updateSchedulerType(3);
                    loadingData(3);
                }
            
            }
            
            
            // Round Robin
            var rr = document.getElementById('rr');
            rr.addEventListener("click", rrClick);
            function rrClick(e) {
            
                // Round Robin is Preemptive only so disable radio buttons
                let nonPreRadioButton = document.getElementById('non-preemptive');
                nonPreRadioButton.checked = false;
                nonPreRadioButton.disabled = true;
                
                let preRadioButton = document.getElementById('preemptive');
                preRadioButton.checked = true;
                preRadioButton.disabled = false;
            
            
                schedulingMethod = "rr";
            
                console.log("Preemptive Round Robin");
                toggleOverlay();
                updateSchedulerType(4);
                loadingData(4);
            
            }
        </script>
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->

        
    </body>
</html>

