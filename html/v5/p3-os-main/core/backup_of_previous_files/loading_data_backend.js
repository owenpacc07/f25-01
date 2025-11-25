//import { readInputDataFile, readOutputDataFile, refreshAnim } from "./main.js";

// JavaScript for how the front-end (webpage) responds when input/output files are being updated on the backend

var flagFileUpdated = false;
var interval;
var procHandler = [];
var procLoad = [];
var numberString = '';
var nextNum;
var processCount = 0;
var sortedLine = [];
var sorted = [];
var otherOutputInformation = [];
var outputLine = [];
let waitingInformation = [];
let responseTimes = [];
let schedulingMethod = "fcfs";


//------------------------------------COPIED FUNCTIONS FROM MAIN.JS-------------------------------------

// New function to read out.dat file as input
function readOutputDataFile(file) {
    // reset data arrays to blank so they will contain only the current output information
    sorted = [];
    otherOutputInformation = [];
    waitingInformation = [];

    var rawFile = new XMLHttpRequest();
    let nextOutputNum = 0;
    let count = 0;
    rawFile.open("GET", file, false);
    rawFile.onreadystatechange = function () {
        if (rawFile.readyState === 4) {
            if (rawFile.status === 200 || rawFile.status == 0) {
                var allText = rawFile.responseText;
                let outputTableInfo = true;

                console.log(allText);

                let arrOutput = allText.slice(3, allText.length);
                arrOutput.split('\n').forEach(function (line) {
                    if (count > 2) {
                        if (line === "") {
                            outputTableInfo = false;
                        }
                        if (outputTableInfo) {
                            let outputString = line;

                            outputString.split(',').forEach(function (number) {
                                nextNum = Number(number);
                                sortedLine.push(nextNum);
                                // <ProcessID, Start Time, End Time>
                                if (sortedLine.length == 3) {
                                    sorted.push(sortedLine);
                                    sortedLine = [];
                                }
                            });
                        } else {

                            if (line != "") {
                                let outputString = line;

                                if (outputString.includes(",")) {
                                    outputString.split(',').forEach(function (outputData) {
                                        nextNum = Number(outputData);
                                        outputLine.push(nextNum);

                                        if (outputLine.length == 7) {
                                            otherOutputInformation.push(outputLine);
                                            outputLine = [];
                                        }
                                    });
                                } else {
                                    nextNum = Number(outputString);
                                    outputLine.push(nextNum);

                                    if (outputLine.length == 4) {
                                        waitingInformation.push(outputLine);
                                        outputLine = [];
                                    }
                                }
                            }
                        }
                    }

                    count++;
                });
            }
        }
    }
    rawFile.send(null);
    console.log(sorted);
    console.log(waitingInformation);
    console.log(otherOutputInformation);
}
function readInputDataFile(file) {
    procHandler = [];
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", file, false);
    rawFile.onreadystatechange = function () {
        if (rawFile.readyState === 4) {
            if (rawFile.status === 200 || rawFile.status == 0) {
                var allText = rawFile.responseText;
                let count = 0;

                // for loop, on each newline
                allText.split('\n').forEach(function (line) {
                    numberString = line;

                    let endIndex;
                    let arrNumbers = numberString.split(' ');
                    for (let i = 0; i < arrNumbers.length; i++) {
                        if (arrNumbers[i].includes('/')) {
                            endIndex = i;
                            break;
                        }
                    }

                    if (count === 0) {

                        // Add code to check type of scheduling method

                    } else {
                        arrNumbers.slice(0, endIndex + 1).forEach(function (number) {

                            if (number.includes('/')) {
                                let slashIndex = number.indexOf('/');
                                number = number.substring(0, slashIndex);
                            }

                            nextNum = Number(number);
                            procLoad.push(nextNum);
                            // <ProcessID, Arrival, Burst, Priority>
                            if (procLoad.length == 4) {
                                procHandler.push(procLoad);
                                procLoad = [];
                            }
                        });
                    }

                    count++;
                });
            }
        }
    }
    rawFile.send(null);
    return procHandler;
}
//----------------------------^^^^COPIEDFUNCTIONSFROMMAIN.JS^^^^^^^^^^^^^^^^^^^^------------------


function toggleOverlay() {
    document.getElementById("overlay").style.display = "block";
}

function loadingData(schedulerType) {
    interval = setInterval(checkFlagFile, 500, schedulerType);
}

function checkFlagFile(schedulerType) {
    // data = 0 indicates in the php to check flag
    fetchPHP(0);

    console.log(flagFileUpdated);
    console.log("\n\n\n");

    if (flagFileUpdated) {
        console.log("Clear interval");
        clearInterval(interval);
        resetFlagFile();
        readInputDataFile("../../../files/p3/in.dat");
        readOutputDataFile("../../../files/p3/out.dat");

        switch(schedulerType) {
            case 0:
                fcfsRefresh();
                break;

            case 1:
            case 5:
                sjfRefresh();
                break;

            case 2:
            case 6:
                prioHighRefresh();
                break;

            case 3:
            case 7:
                prioLowRefresh();
                break;
            
            case 4:
                rrRefresh();
                break;

            default:
                console.log("Not valid scheduler type in checkFileFlag()");
        }

        console.log("FILES UPDATED");
        // disables overlay
        document.getElementById("overlay").style.display = "none";
    }
}

function resetFlagFile() {
    let response = "";
    // data = 1 indicates in the php to reset flag
    response = fetchPHP(1);
    flagFileUpdated = false;

    // halts execution until response is complete
    setTimeout(() => console.log("Finished resetting flag file -- done"), 2000);

}

function fetchPHP(type) {
    // Calls a php file that checks the value inside the flag file or overwrites the value
    fetch('manage-flag-file.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: "text=" + type
      })
      .then(response => response.text())
      .then(data => updateVariable(data))
}

function updateVariable(value) {
    console.log("VALUE: " + value);
    if (value == 0)
        flagFileUpdated = false;
    else if (value == 1)
        flagFileUpdated = true;
}


function fcfsRefresh() {
    refreshAnim();

    let stepByStep;
    if (document.getElementById('step-by-step').checked == true)
        stepByStep = true;
    else
        stepByStep = false;

    var slice = document.getElementById("slice");
    if (slice != null)
        slice.remove();

    document.getElementById('fcfs').style.border = "2px solid dimgray";
    document.getElementById('sjf').style.border = "none";
    document.getElementById('prioHigh').style.border = "none";
    document.getElementById('prioLow').style.border = "none";
    document.getElementById('rr').style.border = "none";

    if (stepByStep) {
        document.getElementById('start').disabled = false;
        document.getElementById('play').disabled = true;
    }
    else {
        document.getElementById('play').disabled = false;
        document.getElementById('start').disabled = true;
    }
}

function sjfRefresh() {
    refreshAnim();

    let stepByStep;
    if (document.getElementById('step-by-step').checked == true)
        stepByStep = true;
    else
        stepByStep = false;

    var slice = document.getElementById("slice");
    if (slice != null)
        slice.remove();
    document.getElementById('fcfs').style.border = "none";
    document.getElementById('sjf').style.border = "2px solid dimgray";
    document.getElementById('prioHigh').style.border = "none";
    document.getElementById('prioLow').style.border = "none";
    document.getElementById('rr').style.border = "none";

    if (stepByStep) {
        document.getElementById('start').disabled = false;
        document.getElementById('play').disabled = true;
    }
    else {
        document.getElementById('play').disabled = false;
        document.getElementById('start').disabled = true;
    }
}

function prioHighRefresh() {
    refreshAnim();

    let stepByStep;
    if (document.getElementById('step-by-step').checked == true)
        stepByStep = true;
    else
        stepByStep = false;

    var slice = document.getElementById("slice");
    if (slice != null)
        slice.remove();
    document.getElementById('fcfs').style.border = "none";
    document.getElementById('sjf').style.border = "none";
    document.getElementById('prioHigh').style.border = "2px solid dimgray";
    document.getElementById('prioLow').style.border = "none";
    document.getElementById('rr').style.border = "none";

    if (stepByStep) {
        document.getElementById('start').disabled = false;
        document.getElementById('play').disabled = true;
    }
    else {
        document.getElementById('play').disabled = false;
        document.getElementById('start').disabled = true;
    }
}

function prioLowRefresh() {
    refreshAnim();

    let stepByStep;
    if (document.getElementById('step-by-step').checked == true)
        stepByStep = true;
    else
        stepByStep = false;

    var slice = document.getElementById("slice");
    if (slice != null)
        slice.remove();
    document.getElementById('fcfs').style.border = "none";
    document.getElementById('sjf').style.border = "none";
    document.getElementById('prioHigh').style.border = "none";
    document.getElementById('prioLow').style.border = "2px solid dimgray";
    document.getElementById('rr').style.border = "none";

    if (stepByStep) {
        document.getElementById('start').disabled = false;
        document.getElementById('play').disabled = true;
    }
    else {
        document.getElementById('play').disabled = false;
        document.getElementById('start').disabled = true;
    }
}

function rrRefresh() {
    refreshAnim();

    let stepByStep;
    if (document.getElementById('step-by-step').checked == true)
        stepByStep = true;
    else
        stepByStep = false;

    var slice = document.createElement("span");
    slice.setAttribute('id', 'slice');
    // var spot = document.getElementById("timeSlice");
    // spot.appendChild(slice);
    slice.innerText = "Time Slice: 4";

    document.getElementById('fcfs').style.border = "none";
    document.getElementById('sjf').style.border = "none";
    document.getElementById('prioHigh').style.border = "none";
    document.getElementById('prioLow').style.border = "none";
    document.getElementById('rr').style.border = "2px solid dimgray";

    if (stepByStep) {
        document.getElementById('start').disabled = false;
        document.getElementById('play').disabled = true;
    }
    else {
        document.getElementById('play').disabled = false;
        document.getElementById('start').disabled = true;
    }
}

export { toggleOverlay, loadingData, readInputDataFile, readOutputDataFile };