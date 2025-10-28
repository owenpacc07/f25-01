<!-- Shortest Job First (Preemptive) -->

<?php 

    session_start();

    //$_SESSION['userid'];
    $mID  =$_SESSION['mechanismid'];

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
        <title>Shortest Job First (Preemptive)</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	     
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
        <script type="module" src="javascript/m-006/main.js" defer></script>
    </head>
    <body>
        <?php include '../navbar.php'; ?> 

        <div id="title-content" class="d-flex align-items-center justify-content-center">
            <button id="back-to-home" type="button" class="btn btn-primary"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>
            <h1 id="title">Shortest Job First (Preemptive)</h1>
        </div>

            <div id="overlay">
                <div id="text">
                    Loading output data... <br>
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>   
            </div>

            <div class="container">
               
                <div id="waitingTime">
                    <p><span class="space"><strong>Waiting Time:</strong></span><span class="space">Exit Time</span> - <span class="space">Arrival Time</span> - <span class="space">Burst Time</span></p>
                    <div id="waitContainer"></div>
                    <!-- <div id="waitTimeAverage"><p id="wait_average_result"></p></div> -->
                    <div id="waitTimeAverage">
                        <div class="averageInformation">
                            <p class="leftEquation">Wait Average:<span class="space"> </span></p>
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
                    <p><span class="space"><strong>Response Time:</strong></span><span class="space">Time at which the process uses the CPU for the first time</span> - <span class="space">Arrival time</span></p>
                    <div id="responseContainer"></div>
                    <div id="responseTimeAverage">
                        <div class="averageInformation">
                            <p class="leftEquation">Response Average:<span class="space"> </span></p>
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
                </div>

                <div id="bottom-container">
                    <div id="title-content" class="d-flex align-items-center justify-content-center">
                        <a class="btn btn-primary" href="editData.php"> EDIT Input/Output DATA </a>
                    </div>
                </div>
            </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
 
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!----------------------------------------------------------------------------------------------------------------------------------------------------------->

        
    </body>
</html>

