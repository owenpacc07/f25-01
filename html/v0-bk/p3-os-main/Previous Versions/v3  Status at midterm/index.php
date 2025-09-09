<?php 
    $input=$fcfs=$sjf=$sjfp=$priority=$priorityp=$roundrobin="";
    if(isset($_POST['submit'])){
        
    }

    if(isset($_POST['load'])){
            
        $input = file_get_contents("../../files/p3/p1-cpu-input.txt");
        $fcfs = file_get_contents("../../files/p3/p1-cpu-output-fcfs.txt");
        $sjf = file_get_contents("../../files/p3/p1-cpu-output-sjf.txt");
        $sjfp= file_get_contents("../../files/p3/p1-cpu-output-sjf-p.txt");
        $priority = file_get_contents("../../files/p3/p1-cpu-output-priority.txt");
        $priorityp = file_get_contents("../../files/p3/p1-cpu-output-priority-p.txt");
        $roundrobin = file_get_contents("../../files/p3/p1-cpu-output-roundrobin.txt");
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
    </head>
    <body>
        <?php include './navbar.php'; ?>  
            <div class="d-flex align-items-center justify-content-center">
                <h1 id="title">CPU Scheduler</h1>
            </div>

            <!--
            <div id="scheduling-method-buttons" class="d-flex align-items-center justify-content-center">
                <input class="btn btn-outline-primary" type="button" value="First Come First Serve" id="fcfs" onclick="fcfs();">
                <input class="btn btn-outline-primary" type="button" value="Shortest Job First" id="sjf" onclick="sjf();">
                <input class="btn btn-outline-primary" type="button" value="Priority" id="prio" onclick="prio();">
                <input class="btn btn-outline-primary" type="button" value="Round Robin" id="rr" onclick="rr();">
            </div>
            -->

            <div id="scheduling-method-buttons" class="d-flex align-items-center justify-content-center">
                <button id="fcfs" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off">First Come First Serve</button>
                <button id="sjf" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off">Shortest Job First</button>
                <button id="prio" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off">Priority</button>
                <button id="rr" type="button" class="btn btn-primary" data-bs-toggle="button" autocomplete="off">Round Robin</button>
            </div>


            <div class="container">
               
                <div id="waitingTime">
                    <p><span class="space"><strong>Waiting Time</strong></span> = <span class="space">Exit Time</span> - <span class="space">Arrival Time</span> - <span class="space">Burst Time</span></p>
                    <div id="waitContainer"></div>
                    <!-- <div id="waitTimeAverage"><p id="wait_average_result"></p></div> -->
                    <div id="waitTimeAverage">
                        <div class="averageInformation">
                            <p class="leftEquation">Average Wait Time<span class="space">=</span></p>
                            <table>
                                <tr>
                                    <td class="numerator">Total Wait Time</td>
                                </tr>
                                <tr>
                                    <td class="denominator">Total Number of Processes</td>
                                </tr>
                            </table>
                        </div>
                        <div class="averageInformation">
                            <p id="averageWaitTimeText"></p>
                            <table id="waitAverageTable">
                                <tr>
                                    <td id="numeratorWait" class="numerator"></td>
                                </tr>
                                <tr>
                                    <td id="denominatorWait" class="denominator"></td>
                                </tr>
                            </table>
                            <p id="waitAverageResult" class="averageResult"></p>
                        </div>
                    </div>
                </div>

                <div id="processInformation">
                    <table class="tableA" id="processTable">
                        <tbody id="procArea">
                            <tr>
                                <th>Process ID</th>
                                <th>Arrival Time</th>
                                <th>Burst Time</th>
                                <th>Priority</th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="responseTime">
                    <p><span class="space"><strong>Response Time</strong></span> = <span class="space">Time at which the process uses the CPU for the first time</span> - <span class="space">Arrival time</span></p>
                    <div id="responseContainer"></div>
                    <div id="responseTimeAverage">
                        <div class="averageInformation">
                            <p class="leftEquation">Average Response Time<span class="space">=</span></p>
                            <table>
                                <tr>
                                    <td class="numerator">Total Response Time</td>
                                </tr>
                                <tr>
                                    <td class="denominator">Total Number of Processes</td>
                                </tr>
                            </table>
                        </div>
                        <div class="averageInformation">
                            <p id="averageResponseTimeText"></p>
                            <table id="responseAverageTable">
                                <tr>
                                    <td id="numeratorResponse" class="numerator"></td>
                                </tr>
                                <tr>
                                    <td id="denominatorResponse" class="denominator"></td>
                                </tr>
                            </table>
                            <p id="responseAverageResult" class="averageResult"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <!--
                <div class="d-flex align-items-center justify-content-center animation-control">
                    <input disabled class="animation-control-button" type="button" value="Start" id="start" onclick="startAnim();">
                    <input disabled class="animation-control-button" type="button" value="Next" id="next" onclick="nextAnim();">
                    <input disabled class="animation-control-button" type="button" value="Back" id="back" onclick="backAnim();">
                    <input disabled class="animation-control-button" type="button" value="Play" id="play">
                    <input disabled class="animation-control-button" type="button" value="Pause" id="pause">
                    <input disabled class="animation-control-button" type="button" value="End" id="end" onclick="endAnim();">
                </div> 
                -->

                <div id="animation-control" class="d-flex align-items-center justify-content-center">
                    <button disabled id="start" type="button" class="btn btn-primary">Start</button>
                    <button disabled id="next" type="button" class="btn btn-primary">Next</button>
                    <button disabled id="back" type="button" class="btn btn-primary">Back</button>
                    <button disabled id="play" type="button" class="btn btn-primary">Play</button>
                    <button disabled id="pause" type="button" class="btn btn-primary">Pause</button>
                    <button disabled id="end" type="button" class="btn btn-primary">End</button>
                </div>




                <div class="d-flex align-items-center justify-content-center refresh-control">
                    <!--
                    <input class="btn btn-primary" type="button" value="Refresh Animation" 
                        id="refresh" onclick="refreshAnim();">
                    -->
                    <button id="refresh" type="button" class="btn btn-outline-primary">Refresh Animation</button>
                </div>
                
                <gantt id="gantt" >
                    <div class="d-flex align-items-center justify-content-center gantt-div">
                    <table class="tableA" id="animationResult">
                        <tbody id="holder">
                            <tr id="head"></tr>
                            <tr id="body"></tr>
                        </tbody>
                    </table>
                    </div>
                </gantt>
            </div>

            <div>
                <!--Animation Control Type Form-->
                <div class="selection">
                    <div class="radio-buttons selection-spacing">
                        <h5 class="pr-3"><strong>Select Animation Type:</strong></h5>
                        <form id="animationType" class="vertical-flex">
                            <input type="radio" id="step-by-step" name="animationType" value="StepByStep" checked>
                            <label for="step-by-step">Step By Step</label>
                            <br>
                            <input type="radio" id="automatic" name="animationType" value="Automatic">
                            <label for="automatic">Automatic</label>
                        </form>
                    </div>
                    <div class="radio-buttons selection-spacing">
                        <h5><strong>Select Scheduling Type:</strong></h5>
                        <form id="preType" class="vertical-flex">
                            <input type="radio" id="non-preemptive" name="preType" value="nonpre" checked>
                            <label for="non-preemptive">Non-Preemptive</label>
                            <br>
                            <input type="radio" id="preemptive" name="preType" value="pre">
                            <label for="preemptive">Preemptive</label>
                        </form>
                    </div>
                </div>

                <div id="bottom-container">
                    <form method="POST" action="index.php">
                        <div class="d-flex align-items-center justify-content-center">
                            <input class="btn btn-primary" id="load-input" name="load" type="submit" value="Load Input">
                        </div>
                    </form>
                    <!--show contents of loaded data
                    <div class="pl-5 has-text-center">
                        <p><strong>Input:</strong><?php echo($input);?></p>
                        <p class="has-text-primary"><strong>Output FCFS:</strong> <?php echo($fcfs);?></p>
                        <p class="has-text-primary"><strong>Output SJF:</strong> <?php echo($sjf);?></p>
                        <p class="has-text-primary"><strong>Output SJF-P:</strong> <?php echo($sjfp);?></p>
                        <p class="has-text-primary"><strong>Output PRIORITY:</strong> <?php echo($priority);?></p>
                        <p class="has-text-primary"><strong>Output PRIORITY-P:</strong> <?php echo($priorityp);?></p>
                        <p class="has-text-primary"><strong>Output ROUND ROBIN:</strong> <?php echo($roundrobin);?></p>
                    </div>
                        -->

                    <form method="POST" action="../files/random.php">
                        <div class="d-flex align-items-center justify-content-center">
                            <input class="btn btn-primary" id="random" type="submit" value="Random">
                            <input id="textfield" type="text" name="AlgoID" value="1">
                        </div>
                    </form>
                </div>
            </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
 
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->

        <script>

            // NOTE:  The sorted array is used to know which output data to use from the readOutputTextFile() function.  The animation is displayed based off of this array.

            if (typeof window !== 'undefined') {
                console.log('You are on the browser');
                } else {
                console.log('You are on the server');
            }


            var table = document.getElementById("processTable");
            var procBody = document.getElementById("procArea");

            var procHandler = [];
            var procLoad = [];
            var numberString = '';
            var nextNum;
            var processCount = 0;

            //function to use php file data as input to animations
            function usePHPinputData(data) {
                data.split('\n').forEach(function(line) {
                    numberString = line;
                    // // for loop, on each newline for each comma
                    numberString.split(',').forEach(function(number){
                        nextNum = Number(number);
                        procLoad.push(nextNum);
                        // <ProcessID, Arrival, Burst, Priority>
                        if(procLoad.length == 4) {
                            procHandler.push(procLoad);
                            procLoad = [];
                        }
                    });
                });
            }
            function readInputTextFile(file)
            {
                var rawFile = new XMLHttpRequest();
                rawFile.open("GET", file, false);
                rawFile.onreadystatechange = function ()
                {
                    if(rawFile.readyState === 4)
                    {
                        if(rawFile.status === 200 || rawFile.status == 0)
                        {
                            var allText = rawFile.responseText;
                            console.log(allText);
                            // for loop, on each newline
                            allText.split('\n').forEach(function(line) {
                                numberString = line;
                                // // for loop, on each newline for each comma
                                numberString.split(',').forEach(function(number){
                                    nextNum = Number(number);
                                    procLoad.push(nextNum);
                                    // <ProcessID, Arrival, Burst, Priority>
                                    if(procLoad.length == 4) {
                                        procHandler.push(procLoad);
                                        procLoad = [];
                                    }
                                });
                            });
                        }
                    }
                }
                rawFile.send(null);
                console.log(procHandler);
            }
            readInputTextFile("../../files/p3/p1-cpu-input.txt");
            console.log(procHandler);

            var numberOfProcesses = procHandler.length;
            const processInfo = 4;

            for(i = 0; i < numberOfProcesses; i++) {
                var newRow = document.createElement('tr');
                newRow.setAttribute('id', 'row'+i);
                procBody.appendChild(newRow);
                newRow.style.cssText = 'background-color: rgb(255, 255, 240);';
                for(j = 0; j < processInfo; j++) {
                    var cell = newRow.insertCell();
                    cell.innerHTML = procHandler[i][j];
                }
            }


            var preemptive = false;
            $('input[type=radio][name=preType]').change(function () {
                if (this.value == 'nonpre') {
                    preemptive = false;
                }
                else if (this.value == 'pre'){
                    preemptive = true;
                }
            });

            //animation type 
            $('input[type=radio][name=animationType]').change(function () {
                if (this.value == 'StepByStep') {
                    //disable play/pause
                    document.getElementById('play').setAttribute('disabled','disabled')
                    document.getElementById('pause').setAttribute('disabled','disabled')
                    document.getElementById('next').removeAttribute("disabled");
                    document.getElementById('back').removeAttribute("disabled");
                    //document.getElementById('play').style.background ="grey"
                    //document.getElementById('pause').style.background = "grey"
                    //document.getElementById('next').classList.add("btn-primary");
                   // document.getElementById('back').classList.add("btn-primary");
                   // document.getElementById('next').removeProperty('background')
                   // document.getElementById('back').removeProperty('background')
                    
                    

                }
                else if (this.value == 'Automatic'){
                    //disable next/back
                    document.getElementById('next').setAttribute('disabled','disabled')
                    document.getElementById('back').setAttribute('disabled','disabled')
                    document.getElementById('play').removeAttribute("disabled");
                    document.getElementById('pause').removeAttribute("disabled");
                   // document.getElementById('next').style.background ="grey"
                    //document.getElementById('back').style.background = "grey"
                    //document.getElementById('play').removeProperty('background')
                   // document.getElementById('pause').removeProperty('background')
                }
            });


            var sorted = [];
            var sortedLine = [];

            function readOutputTextFile(file)
            {
                var rawFile = new XMLHttpRequest();
                rawFile.open("GET", file, false);
                rawFile.onreadystatechange = function ()
                {
                    if(rawFile.readyState === 4)
                    {
                        if(rawFile.status === 200 || rawFile.status == 0)
                        {
                            var allText = rawFile.responseText;
                            allText.split('\n').forEach(function(line) {
                                numberString = line;
                                numberString.split(',').forEach(function(number){
                                    nextNum = Number(number);
                                    sortedLine.push(nextNum);
                                    // <ProcessID, Start Time, End Time>
                                    if(sortedLine.length == 3) {
                                        sorted.push(sortedLine);
                                        sortedLine = [];
                                    }
                                });
                            });
                        }
                    }
                }
                rawFile.send(null);
            }






            // First Come First Serve
            var fcfs = document.getElementById('fcfs');
            fcfs.onclick = function(){
                refreshAnim();

                sorted = [];
                sortedLine = [];
                readOutputTextFile("../../files/p3/p1-cpu-output-fcfs.txt");
                console.log(sorted);
                var slice = document.getElementById("slice");
                if(slice != null)
                    slice.remove();
                    
                document.getElementById('fcfs').style.border = "2px solid dimgray";
		        document.getElementById('sjf').style.border = "none";
		        document.getElementById('prio').style.border = "none";
		        document.getElementById('rr').style.border = "none";

                document.getElementById('start').disabled = false;

                document.getElementById('fcfs').toggle();


                // document.getElementById('fcfs').style.backgroundColor = "rgb(10, 0, 77)";
                // document.getElementById('sjf').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('prio').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('rr').style.backgroundColor = "rgb(86, 60, 255)";

            }

            // Shortest Job First
            var sjf = document.getElementById('sjf');
            sjf.onclick = function() {
                refreshAnim();

                sorted = [];
                sortedLine = [];
                if(preemptive == false)
                    readOutputTextFile("../../files/p3/p1-cpu-output-sjf.txt");
                else if (preemptive == true)
                    readOutputTextFile("../../files/p3/p1-cpu-output-sjf-p.txt");
                console.log(sorted);
                var slice = document.getElementById("slice");
                if(slice != null)
                    slice.remove();
		        document.getElementById('fcfs').style.border = "none";
		        document.getElementById('sjf').style.border = "2px solid dimgray";
		        document.getElementById('prio').style.border = "none";
		        document.getElementById('rr').style.border = "none";

                document.getElementById('start').disabled = false;

                document.getElementById('sjf').toggle();

                // document.getElementById('fcfs').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('sjf').style.backgroundColor = "rgb(10, 0, 77)";
                // document.getElementById('prio').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('rr').style.backgroundColor = "rgb(86, 60, 255)";

            }


            // Priority
            var prio = document.getElementById('prio');
            prio.onclick = function(){
                refreshAnim();

                sorted = [];
                sortedLine = [];
                if(preemptive == false)
                    readOutputTextFile("../../files/p3/p1-cpu-output-priority.txt");
                else if (preemptive == true)
                    readOutputTextFile("../../files/p3/p1-cpu-output-priority-p.txt");
                console.log(sorted);
                var slice = document.getElementById("slice");
                if(slice != null)
                    slice.remove();
		        document.getElementById('fcfs').style.border = "none";
		        document.getElementById('sjf').style.border = "none";
		        document.getElementById('prio').style.border = "2px solid dimgray";
		        document.getElementById('rr').style.border = "none";

                document.getElementById('start').disabled = false;

                document.getElementById('prio').toggle();

                // document.getElementById('fcfs').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('sjf').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('prio').style.backgroundColor = "rgb(10, 0, 77)";
                // document.getElementById('rr').style.backgroundColor = "rgb(86, 60, 255)";

            }


            // Round Robin
            var rr = document.getElementById('rr');
            rr.onclick = function(){
                refreshAnim();

                sorted = [];
                sortedLine = [];
                var slice = document.createElement("span");
                slice.setAttribute('id','slice');
                var spot = document.getElementById("timeSlice");
                spot.appendChild(slice);
                slice.innerText = "Time Slice: 4";
                readOutputTextFile("../../files/p3/p1-cpu-output-roundrobin.txt");
                console.log(sorted);

		        document.getElementById('fcfs').style.border = "none";
		        document.getElementById('sjf').style.border = "none";
		        document.getElementById('prio').style.border = "none";
		        document.getElementById('rr').style.border = "2px solid dimgray";

                document.getElementById('start').disabled = false;

                document.getElementById('rr').toggle();

                // document.getElementById('fcfs').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('sjf').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('prio').style.backgroundColor = "rgb(86, 60, 255)";
                // document.getElementById('rr').style.backgroundColor = "rgb(10, 0, 77)";

            }

            console.log(sorted);



            var procIndex = 0;
            const proc = 0;
            const arrive = 1;
            const burst = 2;
            
            var table = document.getElementById("animationResult");
            var head = document.getElementById("head");
            var body = document.getElementById("body");

            // Refreshes the waiting and response calculations
            function refreshWaitAndResponse() {
                for(var i=0; i < numberOfProcesses; i++) {
                    var waitCalculations = document.getElementById('wait_p' + (i+1));
                    waitCalculations.innerHTML = "<span class=\"process_id\">" + "P" + (i+1) + ":</span>";

                    var responseCalculations = document.getElementById('response_p' + (i+1));
                    responseCalculations.innerHTML = "<span class=\"process_id\">" + "P" + (i+1) + ":</span>";

                    // resets average result
                    if (i == procHandler.length-1) {

                        // Waiting Time reset
                        var waitAverageTimeResult = document.getElementById('waitAverageResult');
                        waitAverageTimeResult.innerHTML = "";

                        var averageWaitTimeText = document.getElementById('averageWaitTimeText');
                        averageWaitTimeText.innerHTML = "";

                        var numeratorWait = document.getElementById('numeratorWait');
                        numeratorWait.innerHTML = "";

                        var denominatorWait = document.getElementById('denominatorWait');
                        denominatorWait.innerHTML = "";

                        // var responseAverageTimeResult = document.getElementById('response_average_result');
                        // responseAverageTimeResult.innerHTML = "";

                        // Response Time reset
                        var responseAverageTimeResult = document.getElementById('responseAverageResult');
                        responseAverageTimeResult.innerHTML = "";

                        var averageResponseTimeText = document.getElementById('averageResponseTimeText');
                        averageResponseTimeText.innerHTML = "";

                        var numeratorResponse = document.getElementById('numeratorResponse');
                        numeratorResponse.innerHTML = "";

                        var denominatorResponse = document.getElementById('denominatorResponse');
                        denominatorResponse.innerHTML = "";
                    }
                }
            }

            function refreshAnim(){
                var count = document.getElementById('animationResult').rows[1].cells.length;
                for(var i = 0; i < count; i++) {
                    $("#animationResult").find("td:last-child").remove();
                    $("#animationResult").find("th:last-child").remove();
                }

                var row = document.getElementById("row"+i);
                for(var i = 0; i < numberOfProcesses; i++){
                    var row = document.getElementById("row"+i);
                    row.style.cssText = 'background-color: rgb(255, 255, 240);';
                }

                // Refreshes the waiting and response calculations
                refreshWaitAndResponse();

                procIndex = 0;
                procNum = -1;

                document.getElementById('start').disabled = false;
                document.getElementById('back').disabled = true;
                document.getElementById('next').disabled = true;
                document.getElementById('end').disabled = true;
            }


            // REFRESH ANIMATION BUTTON
            var refreshButton = document.getElementById('refresh');
            refreshButton.onclick = function() {
                refreshAnim();
            }

            
            // START BUTTON
            var startButton = document.getElementById('start');
            startButton.onclick = function() {
                var count = document.getElementById('animationResult').rows[0].cells.length;
                var head = document.getElementById("head");
                var body = document.getElementById("body");

                var newHead = document.createElement('th');
                var newStart = document.createElement('td');
                var newFinish = document.createElement('td');
                if(count == 0) {
                    refreshAnim();
                    newHead.style.cssText = 'height: 60px; width: ' + sorted[0][burst] * 20 + 'px;';
                    newHead.innerText = 'P' + sorted[0][proc];
                    newStart.innerText = sorted[0][arrive];
                    newFinish.innerText = sorted[0][burst];

                    head.appendChild(newHead);
                    body.appendChild(newStart);
                    body.appendChild(newFinish);
                } else {
                    refreshAnim();
                    newHead.style.cssText = 'height: 60px; width: ' + sorted[0][burst] * 20 + 'px;';
                    newHead.innerText = 'P' + sorted[0][proc];
                    newStart.innerText = sorted[0][arrive];
                    newFinish.innerText = sorted[0][burst];

                    head.appendChild(newHead);
                    body.appendChild(newStart);
                    body.appendChild(newFinish);

                }

                var firstProc = sorted[0][0];
                firstProc--;
                var firstRow = document.getElementById("row"+firstProc);
                for(var i = 0; i < numberOfProcesses; i++){
                    if(i==firstProc)
                        firstRow.style.cssText= 'background-color: lightgreen;';
                    else {
                        var nextRow = document.getElementById("row"+i);
                        nextRow.style.cssText= 'background-color: yellow;';
                    }
                }


                // Calls the waiting and response time functions and generates the html for webpage
                generateWaitingTime(procIndex);
                generateResponseTime(procIndex);
                procIndex = 1;


                // Disables the proper buttons
                document.getElementById('start').disabled = true;
                document.getElementById('next').disabled = false;
                document.getElementById('back').disabled = true;
                document.getElementById('end').disabled = false;
            }


            // NEXT BUTTON
            var nextButton = document.getElementById('next');
            nextButton.onclick = function(){
                var table = document.getElementById("animationResult");
                var head = document.getElementById("head");
                var body = document.getElementById("body");
                var newHead = document.createElement('th');
                var newStart = document.createElement('td');
                var newFinish = document.createElement('td');
                var newBurst = sorted[procIndex][burst] - sorted[procIndex][arrive];
                newHead.style.cssText = 'height: 60px; width: ' + newBurst * 20 + 'px;';
                newHead.innerText = 'P' + sorted[procIndex][proc];
                newFinish.innerText = sorted[procIndex][burst];

                head.appendChild(newHead);
                body.appendChild(newFinish);

                var currentProc = sorted[procIndex][0];
                currentProc--;
                var currentRow = document.getElementById("row"+currentProc);
                for(var i = 0; i < numberOfProcesses; i++){
                    if(i==currentProc)
                        currentRow.style.cssText= 'background-color: lightgreen;';
                    else {
                        var nextRow = document.getElementById("row"+i);
                        nextRow.style.cssText= 'background-color: yellow;';
                    }
                }

                generateWaitingTime(procIndex);
                generateResponseTime(procIndex);
                procIndex++;

                document.getElementById('start').disabled = true;
                document.getElementById('back').disabled = false;

                if (procIndex == numberOfProcesses) {
                    document.getElementById('end').disabled = true;
                    document.getElementById('next').disabled = true;
                } else {
                    document.getElementById('end').disabled = false;
                    document.getElementById('next').disabled = false;
                }
            }


            // BACK BUTTON
            var backButton = document.getElementById('back');
            backButton.onclick = function() {
                $("#animationResult").find("td:last-child").remove();
                $("#animationResult").find("th:last-child").remove();
                procIndex--;

                var currentProc = sorted[procIndex][0];
                currentProc -= 2;
                var currentRow = document.getElementById("row"+currentProc);
                for(var i = 0; i < numberOfProcesses; i++){
                    if(i==currentProc)
                        currentRow.style.cssText= 'background-color: lightgreen;';
                    else {
                        var nextRow = document.getElementById("row"+i);
                        nextRow.style.cssText= 'background-color: yellow;';
                    }
                }

                refreshWaitAndResponse();

                for (var i=0; i < procIndex; i++) {
                    generateWaitingTime(i);
                    generateResponseTime(i);
                }

                document.getElementById('start').disabled = true;
                document.getElementById('next').disabled = false;
                document.getElementById('end').disabled = false;

                if (procIndex == 0) {
                    document.getElementById('back').disabled = true;
                } else {
                    document.getElementById('back').disabled = false;
                }
            }


            // END BUTTON
            var endButton = document.getElementById('end');
            endButton.onclick = function(){
                refreshAnim();
                var table = document.getElementById("animationResult");
                var head = document.getElementById("head");
                var body = document.getElementById("body");
               
                for(i = 0; i < sorted.length; i++) {
                    if(i == 0) {
                        var newHead = document.createElement('th');
                        var newStart = document.createElement('td');
                        var newFinish = document.createElement('td');
                        newHead.style.cssText = 'height: 60px; width: ' + sorted[i][burst] * 20 + 'px;';
                        newHead.innerText = 'P' + sorted[i][proc];
                        newStart.innerText = sorted[i][arrive];
                        newFinish.innerText = sorted[i][burst];

                        head.appendChild(newHead);
                        body.appendChild(newStart);
                        body.appendChild(newFinish);
                    } else {
                        var newHead = document.createElement('th');
                        var newStart = document.createElement('td');
                        var newFinish = document.createElement('td');
                        var newBurst = sorted[i][burst] - sorted[i][arrive];
                        newHead.style.cssText = 'height: 60px; width: ' + newBurst * 20 + 'px;';
                        newHead.innerText = 'P' + sorted[i][proc];
                        newFinish.innerText = sorted[i][burst];

                        head.appendChild(newHead);
                        body.appendChild(newFinish);
                    }
                }

                var lastProc = procHandler.length;
                lastProc--;

                var chosenProc = sorted[lastProc][0];
                chosenProc--;
                var chosenRow = document.getElementById("row"+chosenProc);
                for(var i = 0; i < numberOfProcesses; i++){
                    if(i==chosenProc)
                        chosenRow.style.cssText= 'background-color: lightgreen;';
                    else {
                        var nextRow = document.getElementById("row"+i);
                        nextRow.style.cssText= 'background-color: yellow;';
                    }
                }

                var count = document.getElementById('animationResult').rows[0].cells.length;
                procIndex = count;

                // pass it with negative 1 to indicate display all
                generateResponseTime(-1);
                generateWaitingTime(-1);

                document.getElementById('back').disabled = false;
            }

            var timeInterval;
            var procNum = -1;

            var start = document.getElementById("play");
            start.addEventListener("click", function(){
                timeInterval = setInterval(function(){
                    procNum += 1;
                    var table = document.getElementById("animationResult");
                    var head = document.getElementById("head");
                    var body = document.getElementById("body");
                    var newHead = document.createElement('th');
                    var newStart = document.createElement('td');
                    var newFinish = document.createElement('td');

                    if(procNum == 0) {
                        newHead.style.cssText = 'height: 60px; width: ' + sorted[procNum][burst] * 20 + 'px;';
                        newHead.innerText = 'P' + sorted[procNum][proc];
                        newStart.innerText = sorted[procNum][arrive];
                        newFinish.innerText = sorted[procNum][burst];

                        head.appendChild(newHead);
                        body.appendChild(newStart);
                        body.appendChild(newFinish);
                    } else {
                        var newBurst = sorted[procNum][burst] - sorted[procNum][arrive];
                        newHead.style.cssText = 'height: 60px; width: ' + newBurst * 20 + 'px;';
                        newHead.innerText = 'P' + sorted[procNum][proc];
                        newFinish.innerText = sorted[procNum][burst];

                        head.appendChild(newHead);
                        body.appendChild(newFinish);
                    }

                    var currentProc = sorted[procNum][0];
                    currentProc--;
                    var currentRow = document.getElementById("row"+currentProc);
                    for(var i = 0; i < numberOfProcesses; i++){
                        if(i==currentProc)
                            currentRow.style.cssText= 'background-color: lightgreen;';
                        else {
                            var nextRow = document.getElementById("row"+i);
                            nextRow.style.cssText= 'background-color: yellow;';
                        }
                    }
                    //procIndex += 1;
                }, 1000)
            });

            var pause = document.getElementById("pause");
            pause.addEventListener("click", function(){
                clearInterval(timeInterval);
            })


            //======================================================================================
            console.log("Process Count: " + procHandler.length);

            // Generates the wait and response elements in html
            var parentElement, childElement, appendChildElement, childNewLine;

            parentElement = document.getElementById('waitContainer');
            for (var i=0; i < procHandler.length; i++) {
                childElement = document.createElement('p');
                childElement.setAttribute('id', 'wait_p' + (i+1));
                childNewLine = document.createElement('br');

                childElement.innerHTML = "<span class=\"process_id\">" + "P" + (i+1) + ":</span>";

                appendChildElement = parentElement.appendChild(childElement, childNewLine);
                //appendChildElement.innerHTML = "<span class=\"process_id\">" + "P" + (i+1) + ":</span>";
            }

            parentElement = document.getElementById('responseContainer');
            for (var i=0; i < procHandler.length; i++) {
                childElement = document.createElement('p');
                childElement.setAttribute('id', 'response_p' + (i+1));
                childNewLine = document.createElement('br');

                childElement.innerHTML = "<span class=\"process_id\">" + "P" + (i+1) + ":</span>";

                appendChildElement = parentElement.appendChild(childElement, childNewLine);
                //appendChildElement.innerHTML = "<span class=\"process_id\">" + "P" + (i+1) + ":</span>";
            }


            function calculateWaitingTimeAverage() {
                
                var totalWaitTime = 0, currentProcess, arrivalTime, exitTime, burstTime, waitTime;
                var allWaitTimes = [], allResponseTimes = [];

                // Waiting Time Average
                for (i = 0; i < sorted.length; i++) {
                        currentProcess = sorted[i][0] - 1;

                        // Calculate Wait Time
                        arrivalTime = procHandler[currentProcess][1];
                        exitTime = sorted[i][2];
                        burstTime = procHandler[currentProcess][2];
                        waitTime = exitTime - arrivalTime - burstTime;
                        totalWaitTime+= waitTime;
                        allWaitTimes.push(waitTime);


                        // // Adding wait time calculations to html page
                        var currentProcessTag = document.getElementById('wait_p' + (sorted[i][0]));
                        var processNumber = "<span class=\"process_id\">P" + (currentProcess+1) + ":</span>";
                    
                        currentProcessTag.innerHTML = processNumber + exitTime + " - " + arrivalTime + " - " + burstTime + " = " + waitTime;
                    }

                var average = totalWaitTime/(numberOfProcesses*1.0)
                var averageWaitTimeText = document.getElementById('averageWaitTimeText');
                var numeratorWait = document.getElementById('numeratorWait');
                var denominatorWait = document.getElementById('denominatorWait');
                var averageResultTag = document.getElementById('waitAverageResult');

                averageWaitTimeText.innerHTML = "Average Wait Time <span class=\"space\">=</span>";
                for (var i = 0; i < sorted.length; i++) {

                    numeratorWait.innerHTML+= allWaitTimes[i];
                    if (i !== sorted.length-1) {
                        numeratorWait.innerHTML+= " + ";
                    }
                }
                denominatorWait.innerHTML = numberOfProcesses;
                averageResultTag.innerHTML = "<span class=\"space\"> = </span>" + average;
            }


            function calculateResponseTimeAverage() {

                var totalResponseTime = 0, currentProcess, arrivalTime, processIdToCalculateArrival, processInitialCPUTime, responseTime;
                var allResponseTimes = [];

                // Waiting Time Average
                for (i = 0; i < sorted.length; i++) {
                        currentProcess = sorted[i][0] - 1;

                        // Exit time in the gantt chart
                        processInitialCPUTime = sorted[i][2];

                        processIdToCalculateArrival = sorted[i][0] - 1;
                        arrivalTime = procHandler[processIdToCalculateArrival][1];

                        responseTime = processInitialCPUTime - arrivalTime;
                        totalResponseTime+= responseTime;
                        allResponseTimes.push(responseTime)
                    }

                var average = totalResponseTime/(numberOfProcesses*1.0)
                var averageResponseTimeText = document.getElementById('averageResponseTimeText');
                var numeratorResponse = document.getElementById('numeratorResponse');
                var denominatorResponse = document.getElementById('denominatorResponse');
                var averageResultTag = document.getElementById('responseAverageResult');

                averageResponseTimeText.innerHTML = "Average Response Time <span class=\"space\">=</span>";
                for (var i = 0; i < sorted.length; i++) {

                    numeratorResponse.innerHTML+= allResponseTimes[i];
                    if (i !== sorted.length-1) {
                        numeratorResponse.innerHTML+= " + ";
                    }
                }
                denominatorResponse.innerHTML = numberOfProcesses;
                averageResultTag.innerHTML = "<span class=\"space\"> = </span>" + average;

            }


            // Calculate Waiting Time
            // Waiting Time = Exit Time - Arrival Time - Burst Time
            function generateWaitingTime(currentProcessFromOutputFile) {            

                var exitTime, arrivalTime, processIdToCalculateArrival, burstTime, waitTime;

                // if the End button is pushed
                if (currentProcessFromOutputFile == -1) {
                    var currentProcess, totalWaitTime = 0, average, allWaitTimes = [];

                    // loops for each process and retrieves exit and arrival times
                    // Only will occur if the End button is pressed
                    for (i = 0; i < sorted.length; i++) {
                        currentProcess = sorted[i][0] - 1;

                        // Calculate Wait Time
                        arrivalTime = procHandler[currentProcess][1];
                        exitTime = sorted[i][2];
                        burstTime = procHandler[currentProcess][2];
                        waitTime = exitTime - arrivalTime - burstTime;
                        // totalWaitTime+= waitTime;
                        // allWaitTimes.push(waitTime);


                        // Adding wait time calculations to html page
                        var currentProcessTag = document.getElementById('wait_p' + (sorted[i][0]));
                        var processNumber = "<span class=\"process_id\">P" + (currentProcess+1) + ":</span>";
                    
                        currentProcessTag.innerHTML = processNumber + exitTime + " - " + arrivalTime + " - " + burstTime + " = " + waitTime;
                    }

                    // Calculates Average Wait Time
                    calculateWaitingTimeAverage();

                } 
                // Occurs when the Start or Next button are pressed.
                // If currentProcessFromOutputFile == 0 then Start was pressed.
                // If currenetProcessFromOutputFile > 0 then Next/Back was pressed.
                else if (currentProcessFromOutputFile > -1) {

                    processIdToCalculateArrival = sorted[currentProcessFromOutputFile][0] - 1;

                    // Calculate Wait Time
                    arrivalTime = procHandler[processIdToCalculateArrival][1];
                    exitTime = sorted[currentProcessFromOutputFile][2];
                    burstTime = procHandler[processIdToCalculateArrival][2];
                    waitTime = exitTime - arrivalTime - burstTime;


                    // Adding Wait Time Calculations to html page
                    console.log(sorted[currentProcessFromOutputFile][0] - 1);
                    var currentProcessTag = document.getElementById('wait_p' + (sorted[currentProcessFromOutputFile][0]));
                    var processNumber = "<span class=\"process_id\">" + currentProcessTag.innerText + "</span>";
                    
                    currentProcessTag.innerHTML = processNumber + exitTime + " - " + arrivalTime + " - " + burstTime + " = " + waitTime;


                    // Calculates Average Wait Time on the last process
                    if ((currentProcessFromOutputFile+1) == numberOfProcesses) {
                        calculateWaitingTimeAverage();
                    }


                    // ============ DEBUGGING =============
                    // console.log("Process Id: " + currentProcessFromOutputFile);
                    // console.log("Arrival: " + arrivalTime);
                    // console.log("Exit: " + exitTime);
                    // console.log("Burst: " + burstTime);
                    // console.log("WAIT TIME: " + waitTime);

                } else {
                    console.log("Invalid Index in generateWaitingTime() function");
                }
                
                
            }

            // Calculate Response Time
            // Response Time = Time at which the process gets the CPU for the first time - Arrival time
            function generateResponseTime(currentProcessFromOutputFile) {
                
                var responseTime, arrivalTime, processIdToCalculateArrival, processInitialCPUTime;

                // Only will occur if the End button is pressed
                if (currentProcessFromOutputFile == -1) {

                    var currentProcess;

                    // loops for each process and retrieves exit and arrival times
                    // Only will occur if the End button is pressed
                    for (var i = 0; i < sorted.length; i++) {
                        currentProcess = sorted[i][0] - 1;

                        // Exit time in the gantt chart
                        processInitialCPUTime = sorted[i][2];

                        processIdToCalculateArrival = sorted[i][0] - 1;
                        arrivalTime = procHandler[processIdToCalculateArrival][1];

                        responseTime = processInitialCPUTime - arrivalTime;

                        // Adding response time calculations to html page
                        var currentProcessTag = document.getElementById('response_p' + (sorted[i][0]));
                        var processNumber = "<span class=\"process_id\">P" + (currentProcess+1) + ":</span>";
                    
                        currentProcessTag.innerHTML = processNumber + processInitialCPUTime + " - " + arrivalTime + " = " + responseTime;
                    }

                    // Calculates Average Response Time
                    calculateResponseTimeAverage();

                }
                // Occurs when the Start or Next button are pressed
                else if (currentProcessFromOutputFile > -1) {

                    // Exit time in the gantt chart
                    processInitialCPUTime = sorted[currentProcessFromOutputFile][2];

                    processIdToCalculateArrival = sorted[currentProcessFromOutputFile][0] - 1;
                    arrivalTime = procHandler[processIdToCalculateArrival][1];

                    responseTime = processInitialCPUTime - arrivalTime;


                    // Adding response time to html
                    var currentProcessTag = document.getElementById('response_p' + (sorted[currentProcessFromOutputFile][0]));
                    var processNumber = "<span class=\"process_id\">" + currentProcessTag.innerText + "</span>";
                    
                    currentProcessTag.innerHTML = processNumber + processInitialCPUTime + " - " + arrivalTime + " = " + responseTime;

                    // Calculates Average Response Time on the last process
                    if ((currentProcessFromOutputFile+1) == numberOfProcesses) {
                        calculateResponseTimeAverage();
                    }

                } else {
                    console.log("Error in the generateResponseTime(). Passed invalid index of " + currentProcessFromOutputFile + ".");
                }


            }

        </script>
    </body>
</html>

