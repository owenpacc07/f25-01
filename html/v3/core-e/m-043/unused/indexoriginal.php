<?php

// TEMPLATE

require_once "../../config-legacy.php";
global $link;
session_start();

$mid = '041'; // <-- PUT your mechanism ID HERE
$mtitle = '(Your Mechanism Title)';

// get mechanism title based on mid
$sql = "SELECT algorithm FROM mechanisms WHERE client_code = {$mid}";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);
$mtitle = $row['algorithm'];

// run java code 
shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-{$mid} m{$mid}");
?>

<!DOCTYPE html>
<html>

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
    <!-- allows mid to be accessible to all js files -->
    <script>
        var mid = `<?php echo $mid; ?>`;
    </script>
    <script type="module" src="main.js" defer></script>
</head>

<body>

    <?php include '../../corenavbar.php'; ?>

    <div id="title-content" class="d-flex align-items-center justify-content-center">
        <h1 id="title">Disk Scheduling - <?= $mtitle ?></h1>
    </div>

    <div class="containerFluid">
        <p class="text-center">Current Algorithm: <span id="algorithm-name"></span></p>
        <h4 class="text-center">Queue and Requests</h4>

        <div class="grid-container border">
            <div class="item1">
                <p>Queue: <span class="data" id="the-queue">Queue</span></p>
            </div>
            <div class="item2">
                <p>Head: <span class="data" id="the-head">Head</span></p>
            </div>
            <div class="item3">
                <p>Current Request (in Queue): <span class="data" id="timer-text"></span></p>
            </div>
        </div>

        <hr>
        <div class="">
            <div class="container">
                <!--Animation Control Type Form-->
                <div class="d-flex align-items-center justify-content-center">
                    <h5 class="pr-3">Select Animation Type: </h5>
                    <form id="animationType">
                        <input type="radio" name="animationType" value="StepByStep" checked /> Step By Step<br />
                        <input type="radio" name="animationType" value="Automatic" /> Automatic<br />
                    </form>
                </div>
                <hr>
                <div class="text-center align-items-center justify-content-center">
                    <button class="btn btn-info" type="button" id="Previous" onclick=" clearLine();">Previous</button>
                    <button class="btn btn-info" type="button" id="Next" onclick="displayLine();">Next </button>
                    _
                    <button class="btn btn-info" type="button" disabled id="play" onclick="">Play </button>
                    <button class="btn btn-info" type="button" disabled id="pause" onclick="">Pause </button>
                    _
                    <button class="btn btn-info" type="button" id="Start" onclick="clearAll();">Start </button>
                    <button class="btn btn-info" type="button" id="End" onclick="displayAll();">End </button>
                    _
                    <button class="btn btn-info" type="button" id="Reset" onclick="Reset();">Reset </button>
                </div>
                <br>
                <img src="Capture.JPG" alt="Lines" width="704">
                <canvas id="myCanvas" width="700" height="500">
                    Your browser does not support the HTML5 canvas tag.
                </canvas>
                <img src="Capture2.JPG" alt="Lines" width="704">
            </div>
        </div>


        <script>
            initalValues = [];
            initalValues2 = [];
            values = [];
            index = 0;
            head = 0;
            verticalindex = 0;
            var c = document.getElementById("myCanvas");
            var ctx = c.getContext("2d");
            ctx.beginPath();

            // where the input and output files are being read from
            var inputFileLocation = "../../files/p6-disk-input.txt";
            var outputFileLocationFCFS = "../../files/p6-disk-output-fcfs.txt";
            var outputFileLocationSSTF = "../../files/p6-disk-output-sstf.txt";
            var outputFileLocationCSCAN = "../../files/p6-disk-output-cscan.txt";
            var outputFileLocationLOOK = "../../files/p6-disk-output-look.txt";
            var outputFileLocationCLOOK = "../../files/p6-disk-output-clook.txt";

            var procHandler = [];
            var procLoad = [];
            var numberString = '';
            var nextNum;
            var numCount = 0;

            var procHandlerOut = [];
            var procLoadOut = [];
            var numCountout = 0;

            // the buttons
            const timerText = document.getElementById("timer-text");
            const btnStart = document.getElementById("play");
            const btnPause = document.getElementById("pause");
            const btnRand = document.getElementById("btn-rand");

            var count = 0;
            var intervalID;

            btnStart.addEventListener("click", function() {
                intervalID = setInterval(function() {
                    if (count <= values.length) {
                        displayLinePlay(count);
                        count += 1;
                        timerText.textContent = values[count - 1];
                    }
                }, 1000);

            });

            btnPause.addEventListener("click", function() {
                clearInterval(intervalID);
            });

            // buttons for changing animation type 
            $('input[type=radio][name=animationType]').change(function() {
                if (this.value == 'StepByStep') {
                    //disable play/pause
                    document.getElementById('play').setAttribute('disabled', 'disabled')
                    document.getElementById('pause').setAttribute('disabled', 'disabled')
                    document.getElementById('Next').removeAttribute("disabled");
                    document.getElementById('Previous').removeAttribute("disabled");
                } else if (this.value == 'Automatic') {
                    //disable next/back
                    document.getElementById('Next').setAttribute('disabled', 'disabled')
                    document.getElementById('Previous').setAttribute('disabled', 'disabled')
                    document.getElementById('play').removeAttribute("disabled");
                    document.getElementById('pause').removeAttribute("disabled");
                }
            });

            // resets all variables, visuals, and text
            function Reset() {
                values = [];
                index = 0;
                head = 0;
                verticalindex = 0;
                count = 0;
                document.getElementById("algorithm-name").innerHTML = "";
                document.getElementById("the-queue").innerHTML = "";
                document.getElementById("the-head").innerHTML = "";
                ctx.clearRect(0, 0, 700, 500);
                ctx.beginPath();
                procHandler = [];
                procLoad = [];
                numberString = '';
                nextNum;
                numCount = 0;

                count = 0;
                timerText.textContent = "";

                procHandlerOut = [];
                procLoadOut = [];
                numCountout = 0;
                clearInterval(intervalID);
                initalValues = [];
            }

            // changes the formatting of the webpage based on which algorithm is selected
            function runProgram(mode) {
                // mode = algorithm
                // 1    = FCFS
                // 2    = SSTF
                // 3    = CSCAN
                // 4    = LOOK
                // 5    = CLOOK
                Reset();
                readInputTextFile(inputFileLocation);
                // resets button themes
                document.getElementById('FCFS').style.border = "none";
                document.getElementById('SSTF').style.border = "none";
                document.getElementById('CSCAN').style.border = "none";
                document.getElementById('LOOK').style.border = "none";
                document.getElementById('CLOOK').style.border = "none";
                // changes theming based on which option is selected
                switch (mode) {
                    //FCFS
                    case 1:
                        readOutputTextFile(outputFileLocationFCFS);
                        document.getElementById("algorithm-name").innerHTML = "FCFS (First Come First Serve)";
                        document.getElementById('FCFS').style.border = "2px solid orange";
                        break;
                        //SSTF
                    case 2:
                        readOutputTextFile(outputFileLocationSSTF);
                        document.getElementById("algorithm-name").innerHTML = " SSTF (Shortest Seek Time First)";
                        document.getElementById('SSTF').style.border = "2px solid orange";
                        break;
                        //CSCAN
                    case 3:
                        readOutputTextFile(outputFileLocationCSCAN);
                        document.getElementById("algorithm-name").innerHTML = "CSCAN";
                        document.getElementById('CSCAN').style.border = "2px solid orange";
                        break;
                        //LOOK
                    case 4:
                        readOutputTextFile(outputFileLocationLOOK);
                        document.getElementById("algorithm-name").innerHTML = "LOOK";
                        document.getElementById('LOOK').style.border = "2px solid orange";
                        break;
                        //CLOOK
                    case 5:
                        readOutputTextFile(outputFileLocationCLOOK);
                        document.getElementById("algorithm-name").innerHTML = "CLOOK";
                        document.getElementById('CLOOK').style.border = "2px solid orange";
                        break;
                }
                document.getElementById("the-queue").innerHTML = initalValues;
                document.getElementById("the-head").innerHTML = head;
            }

            // this function does nothing but it should randomize the input
            function randomizeInputs() {
                //
            }

            function readInputTextFile(file) {
                var rawFile = new XMLHttpRequest();
                rawFile.open("GET", file, false);
                // console.log(file);
                rawFile.onreadystatechange = function() {
                    if (rawFile.readyState === 4) {
                        if (rawFile.status === 200 || rawFile.status == 0) {
                            var allText = rawFile.responseText;
                            allText.split('\n').forEach(function(line) {
                                numberString = line;
                                numberString.split(' ').forEach(function(number) {
                                    nextNum = Number(number);
                                    procLoad.push(nextNum);
                                    //console.log(nextNum);
                                    if (numCount == 2) {
                                        head = nextNum;
                                    }
                                    if (numCount >= 3) {
                                        procHandler.push(procLoad[numCount]);
                                        //console.log(procLoad[numCount]);
                                    }
                                    numCount++;
                                });
                            });
                            initalValues = procHandler;
                            initalValues.pop(initalValues.length);
                            //console.log(initalValues);
                        }
                    }
                }
                rawFile.send(null);
            }

            function readOutputTextFile(file) {
                var rawFile = new XMLHttpRequest();
                rawFile.open("GET", file, false);
                rawFile.onreadystatechange = function() {
                    if (rawFile.readyState === 4) {
                        if (rawFile.status === 200 || rawFile.status == 0) {
                            var allText = rawFile.responseText;
                            allText.split('\n').forEach(function(line) {
                                numberString = line;
                                numberString.split(' ').forEach(function(number) {
                                    nextNum = Number(number);
                                    procLoadOut.push(nextNum);
                                    ///console.log(nextNum);
                                    if (numCountout == 2) {
                                        head = nextNum;
                                    }
                                    if (numCountout >= 3) {
                                        procHandlerOut.push(procLoadOut[numCountout]);
                                        //console.log(procLoadOut[numCountout]);
                                    }
                                    numCountout++;
                                });
                            });
                            //remove the weird leftover zero
                            procHandlerOut.pop(initalValues.length);
                            //console.log(procHandlerOut);
                            values = procHandlerOut;
                        }
                    }
                }
                rawFile.send(null);
            }

            // these are animation functions that i have not figured out yet but am working on
            //
            // should display the next line in the sequence
            function displayLine() {
                ctx.beginPath();
                if (index == 0) {
                    ctx.moveTo(head, 0);
                } else {
                    ctx.moveTo(values[index - 1], verticalindex);
                }
                verticalindex = verticalindex + 25;
                ctx.lineTo(values[index], verticalindex);
                ctx.stroke();
                index++;
                count += 1;
                timerText.textContent = values[count - 1];
            }

            function displayLinePlay(cnt) {
                ctx.beginPath();
                if (cnt == 0) {
                    ctx.moveTo(head, 0);
                } else {
                    ctx.moveTo(values[cnt - 1], verticalindex);
                }
                verticalindex = verticalindex + 25;
                ctx.lineTo(values[cnt], verticalindex);
                ctx.stroke();
            }

            function displayAll() {
                ctx.beginPath();

                localverticalindex = 25;
                for (var i = 0; i < values.length; i++) {
                    if (i == 0) {
                        ctx.moveTo(head, 0);
                        ctx.lineTo(values[i], localverticalindex);
                    } else {
                        ctx.moveTo(values[i - 1], localverticalindex);
                        localverticalindex = localverticalindex + 25;
                        ctx.lineTo(values[i], localverticalindex);
                    }
                }
                ctx.stroke();
                localverticalindex = 25;
            }

            function clearLine() {
                if (values[index - 1] == undefined) {
                    if (head < values[index]) {
                        ctx.clearRect(head, verticalindex, values[index], verticalindex + 25);
                    } else {
                        ctx.clearRect(values[index], verticalindex, head, verticalindex + 25);
                    }
                    verticalindex = 25;
                }
                if (values[index - 1] < values[index]) {
                    ctx.clearRect(values[index - 1], verticalindex, values[index], verticalindex + 25);
                } else {
                    ctx.clearRect(values[index], verticalindex, values[index - 1], verticalindex + 25);
                }
                verticalindex = verticalindex - 25;
                index--;
                //console.log("values[index - 1]:" + values[index-1]  + " verticalindex:" + verticalindex + " values[index]:" + values[index] + " verticalindex + 25:" + (verticalindex + 25));
                count -= 1;
                timerText.textContent = values[count - 1];
            }

            function clearAll() {
                ctx.clearRect(0, 0, 700, 500);
                index = 0;
                verticalindex = 0;
                count = 0;
                timerText.textContent = "";
            }
        </script>


</body>

</html>