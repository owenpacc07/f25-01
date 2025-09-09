/* 
**  Priority
*/

// NOTE:  The sorted array is used to know which output data to use from the readOutputTextFile() function.  The animation is displayed based off of this array.

import { toggleOverlay, loadingData } from "./loading_data_backend.js";
toggleOverlay();
updateSchedulerType(2);
loadingData(2);

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
var sortedLine = [];
var sorted = [];
var otherOutputInformation = [];
var outputLine = [];
let waitingInformation = [];
let responseTimes = [];
let schedulingMethod = "prioHigh";
let animationPlaying = false;

// address to the other CPU Scheduling pages
let first_come_first_serve = 'https://cs.newpaltz.edu/p/f22-02/v3/p3-os-main/core/index.php?page=m-001';
let shortest_job_first = 'https://cs.newpaltz.edu/p/f22-02/v3/p3-os-main/core/index.php?page=m-002';
let priority_high_to_low = 'https://cs.newpaltz.edu/p/f22-02/v3/p3-os-main/core/index.php?page=m-003';
let priority_low_to_high = 'https://cs.newpaltz.edu/p/f22-02/v3/p3-os-main/core/index.php?page=m-004';
let round_robin = 'https://cs.newpaltz.edu/p/f22-02/v3/p3-os-main/core/index.php?page=m-005';

// Input and output file paths
let input_file_path = 'https://cs.newpaltz.edu/p/f22-02/files/p3/in.dat';
let output_file_path = 'https://cs.newpaltz.edu/p/f22-02/files/p3/out.dat';

// Enable Buttons
document.getElementById('start').disabled = false;
console.log("m-003");

let back_to_home = document.getElementById('back-to-home');
back_to_home.addEventListener('click', redirectToHome)
function redirectToHome(e) {
    location.replace('https://cs.newpaltz.edu/p/f22-02/v3/p3-os-main/core/');
}


//function to use php file data as input to animations
function usePHPinputData(data) {
    data.split('\n').forEach(function (line) {
        numberString = line;
        // // for loop, on each newline for each comma
        numberString.split(',').forEach(function (number) {
            nextNum = Number(number);
            procLoad.push(nextNum);
            // <ProcessID, Arrival, Burst, Priority>
            if (procLoad.length == 4) {
                procHandler.push(procLoad);
                procLoad = [];
            }
        });
    });
}

// New function to read in.dat file as input
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

readInputDataFile(input_file_path);

var numberOfProcesses = procHandler.length;
const processInfo = 4;

function loadInputDataTable() {
    for (let i = 0; i < numberOfProcesses; i++) {
        var newRow = document.createElement('tr');
        newRow.setAttribute('id', 'row' + i);
        procBody.appendChild(newRow);
        newRow.style.cssText = 'background-color: rgb(255, 255, 240);';
        for (let j = 0; j < processInfo; j++) {
            var cell = newRow.insertCell();
            cell.innerHTML = procHandler[i][j];
        }
    }
}

loadInputDataTable();

// var preemptive = false;
// $('input[type=radio][name=preType]').change(function () {
//     if (this.value == 'nonpre') {
//         preemptive = false;
//     }
//     else if (this.value == 'pre') {
//         preemptive = true;
//     }
//     changePreemptiveness();
// });

let stepByStep = true;
//animation type 
$('input[type=radio][name=animationType]').change(function () {

    clearInterval(timeInterval);
    animationPlaying = false;
    refreshAnim();

    if (this.value == 'StepByStep') {
        stepByStep = true;

        clearInterval(timeInterval);
        animationPlaying = false;
        refreshAnim();
        document.getElementById('play').disabled = true;
        document.getElementById('start').disabled = false;
        
    }
    else if (this.value == 'Automatic') {
        stepByStep = false;

        document.getElementById('play').disabled = false;
        document.getElementById('start').disabled = true;
        
    }

    document.getElementById('pause').disabled = true;
    document.getElementById('end').disabled = true;
    document.getElementById('next').disabled = true;
    document.getElementById('back').disabled = true;
});


// function disableRadioButtons() {
//     document.getElementById('preemptive').disabled = true;
//     document.getElementById('non-preemptive').disabled = true;
// }

// // User should not be able to check a button until they select a scheduling method
// disableRadioButtons();

var procIndex = 0;
const proc = 0;
const arrive = 1;
const burst = 2;

var table = document.getElementById("animationResult");
var head = document.getElementById("head");
var body = document.getElementById("body");

// Refreshes the waiting and response calculations
function refreshWaitAndResponse() {
    for (let i = 0; i < otherOutputInformation.length; i++) {
        // resets Waiting Time <p> tags
        let waitCalculations = document.getElementById('wait_' + (i));
        if (waitCalculations != null) {
            waitCalculations.innerHTML = "P" + (otherOutputInformation[i][0]) + ":  ";
        }

        // resets Response Time <p> tags
        let responseCalculations = document.getElementById('response_' + (i));
        if (responseCalculations != null) {
            responseCalculations.innerHTML = "P" + (otherOutputInformation[i][0]) + ":  ";
        }
    }

    refreshAverages();
}

function refreshAverages() {
    // Waiting Time reset
    var waitAverageTimeResult = document.getElementById('waitAverageResult');
    waitAverageTimeResult.innerHTML = "";

    var averageWaitTimeText = document.getElementById('averageWaitTimeText');
    averageWaitTimeText.innerHTML = "";

    var numeratorWait = document.getElementById('numeratorWait');
    numeratorWait.innerHTML = "";

    var denominatorWait = document.getElementById('denominatorWait');
    denominatorWait.innerHTML = "";

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

function refreshAnim() {
    // Clears the time internal on refresh animation to stop the automatic animation from constantly looping
    if (animationPlaying)
        clearInterval(timeInterval);

    var count = document.getElementById('animationResult').rows[1].cells.length;
    for (var i = 0; i < count; i++) {
        $("#animationResult").find("td:last-child").remove();
        $("#animationResult").find("th:last-child").remove();
    }

    var row = document.getElementById("row" + i);
    for (var i = 0; i < numberOfProcesses; i++) {
        var row = document.getElementById("row" + i);
        row.style.cssText = 'background-color: rgb(255, 255, 240);';
    }

    // Refreshes the waiting and response calculations
    // refreshWaitAndResponse();
    removeWaitInfo();
    createWaitInfo();
    refreshAverages();
    

    procIndex = 0;
    procNum = -1;

    if (stepByStep) {
        document.getElementById('start').disabled = false;
        document.getElementById('back').disabled = true;
        document.getElementById('next').disabled = true;
        document.getElementById('end').disabled = true;
    } else {
        document.getElementById('play').disabled = false;
        document.getElementById('pause').disabled = true;
    }
}


// REFRESH ANIMATION BUTTON
var refreshButton = document.getElementById('refresh');
refreshButton.onclick = function () {
    refreshAnim();
}


// START BUTTON
var startButton = document.getElementById('start');
startButton.onclick = function () {
    var count = document.getElementById('animationResult').rows[0].cells.length;
    var head = document.getElementById("head");
    var body = document.getElementById("body");

    var newHead = document.createElement('th');
    var newStart = document.createElement('td');
    var newFinish = document.createElement('td');
    if (count == 0) {
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
    var firstRow = document.getElementById("row" + firstProc);
    for (var i = 0; i < numberOfProcesses; i++) {
        if (i == firstProc)
            firstRow.style.cssText = 'background-color: lightgreen;';
        else {
            var nextRow = document.getElementById("row" + i);
            nextRow.style.cssText = 'background-color: yellow;';
        }
    }


    // Calls the waiting and response time functions and generates the html for webpage
    generateWaitingTime(procIndex);
    generateResponseTime(procIndex);
    procIndex = 1;


    // Disables the proper buttons
    if (stepByStep) {
        document.getElementById('start').disabled = true;
        document.getElementById('next').disabled = false;
        document.getElementById('back').disabled = true;
        document.getElementById('end').disabled = false;
    }
}


// NEXT BUTTON
var nextButton = document.getElementById('next');
nextButton.onclick = function () {
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
    var currentRow = document.getElementById("row" + currentProc);
    for (var i = 0; i < numberOfProcesses; i++) {
        if (i == currentProc)
            currentRow.style.cssText = 'background-color: lightgreen;';
        else {
            var nextRow = document.getElementById("row" + i);
            nextRow.style.cssText = 'background-color: yellow;';
        }
    }

    generateWaitingTime(procIndex);
    generateResponseTime(procIndex);
    procIndex++;

    document.getElementById('start').disabled = true;
    document.getElementById('back').disabled = false;

    // Disables buttons if at end of output
    if (procIndex == sorted.length) {
        document.getElementById('end').disabled = true;
        document.getElementById('next').disabled = true;
    } else {
        document.getElementById('end').disabled = false;
        document.getElementById('next').disabled = false;
    }
}


// BACK BUTTON
var backButton = document.getElementById('back');
backButton.onclick = function () {
    $("#animationResult").find("td:last-child").remove();
    $("#animationResult").find("th:last-child").remove();
    procIndex--;

    var currentProc = sorted[procIndex-1][0];
    currentProc -= 1;
    var currentRow = document.getElementById("row" + currentProc);
    console.log(`Current Process: ${currentProc}\nProcess Index: ${procIndex}`);
    for (var i = 0; i < numberOfProcesses; i++) {
        if (i == currentProc)
            currentRow.style.cssText = 'background-color: lightgreen;';
        else {
            var nextRow = document.getElementById("row" + i);
            nextRow.style.cssText = 'background-color: yellow;';
        }
    }

    refreshWaitAndResponse();

    for (var i = 0; i < procIndex; i++) {
        generateWaitingTime(i);
        generateResponseTime(i);
    }

    document.getElementById('start').disabled = true;
    document.getElementById('next').disabled = false;
    document.getElementById('end').disabled = false;

    if (procIndex <= 1) {
        document.getElementById('back').disabled = true;
    } else {
        document.getElementById('back').disabled = false;
    }
}


// END BUTTON
var endButton = document.getElementById('end');
endButton.onclick = function () {
    refreshAnim();
    // Re-disables start button since the refresh animation function enables it
    document.getElementById('start').disabled = true;

    var table = document.getElementById("animationResult");
    var head = document.getElementById("head");
    var body = document.getElementById("body");

    for (i = 0; i < sorted.length; i++) {
        if (i == 0) {
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
    console.log(procHandler);
    var lastProc = procHandler.length;
    lastProc--;

    var chosenProc = sorted[lastProc][0];
    chosenProc--;
    var chosenRow = document.getElementById("row" + chosenProc);
    for (var i = 0; i < numberOfProcesses; i++) {
        if (i == chosenProc)
            chosenRow.style.cssText = 'background-color: lightgreen;';
        else {
            var nextRow = document.getElementById("row" + i);
            nextRow.style.cssText = 'background-color: yellow;';
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
start.addEventListener("click", function () {
    animationPlaying = true;

    timeInterval = setInterval(function () {
        procNum += 1;
        var table = document.getElementById("animationResult");
        var head = document.getElementById("head");
        var body = document.getElementById("body");
        var newHead = document.createElement('th');
        var newStart = document.createElement('td');
        var newFinish = document.createElement('td');

        document.getElementById('play').disabled = true;
        document.getElementById('pause').disabled = false;

        if (procNum == 0) {
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
        var currentRow = document.getElementById("row" + currentProc);
        for (var i = 0; i < numberOfProcesses; i++) {
            if (i == currentProc)
                currentRow.style.cssText = 'background-color: lightgreen;';
            else {
                var nextRow = document.getElementById("row" + i);
                nextRow.style.cssText = 'background-color: yellow;';
            }
        }
        // Displays Wait and Response Times while playing animation
        generateWaitingTime(procNum);
        generateResponseTime(procNum);
        //procIndex += 1;
    }, 1000)
});

var pause = document.getElementById("pause");
pause.addEventListener("click", function () {
    clearInterval(timeInterval);
    animationPlaying = false;

    document.getElementById('play').disabled = false;
    document.getElementById('pause').disabled = true;
})


//======================================================================================
console.log("Process Count: " + procHandler.length);


function generateResponseTime(proc) {

    // reset Response Times array so it is empty
    responseTimes = [];


    let currentResponseTag;
    let arrivalTime, processInitialCPUTime, negativeResponseTime;
    let calculationText = "";
    let minus = "<span class=\"space\">-</span>";
    let equals = "<span class=\"space\">=</span>";
    let arrow = "<span class=\"space\">-></span>";

    if (proc === -1) {
        for (let i = 0; i < otherOutputInformation.length; i++) {
            currentResponseTag = document.getElementById('response_' + (i));

            if (currentResponseTag != null) {

                processInitialCPUTime = sorted[i][1];
                arrivalTime = otherOutputInformation[i][1];

                if ((processInitialCPUTime - arrivalTime) < 0) {
                    negativeResponseTime = true;

                    // pushes 0 as the response time result to an array holding all response times
                    responseTimes.push(0);
                }
                else {
                    negativeResponseTime = false;

                    // pushes the response time result to an array holding all response times
                    responseTimes.push((processInitialCPUTime - arrivalTime));
                }

                if (negativeResponseTime)
                    calculationText = `${processInitialCPUTime} ${minus} ${arrivalTime} ${equals} ${(processInitialCPUTime - arrivalTime)} ${arrow} 0`;
                else
                    calculationText = `${processInitialCPUTime} ${minus} ${arrivalTime} ${equals} ${(processInitialCPUTime - arrivalTime)}`;
                currentResponseTag.innerHTML = "P" + (otherOutputInformation[i][0]) + ":<span class=\"space\">" + calculationText + "</span>";
            }
        }
        displayResponseAverage();
    } else if (proc > -1) {
        for (let i = 0; i < proc + 1; i++) {
            currentResponseTag = document.getElementById('response_' + (i));

            if (currentResponseTag != null) {

                processInitialCPUTime = sorted[i][1];
                arrivalTime = otherOutputInformation[i][1];

                if ((processInitialCPUTime - arrivalTime) < 0) {
                    negativeResponseTime = true;

                    // pushes 0 as the response time result to an array holding all response times
                    responseTimes.push(0);
                }
                else {
                    negativeResponseTime = false;

                    // pushes the response time result to an array holding all response times
                    responseTimes.push((processInitialCPUTime - arrivalTime));
                }

                if (negativeResponseTime)
                    calculationText = `${processInitialCPUTime} ${minus} ${arrivalTime} ${equals} ${(processInitialCPUTime - arrivalTime)} ${arrow} ${otherOutputInformation[i][6]}`;
                else
                    calculationText = `${processInitialCPUTime} ${minus} ${arrivalTime} ${equals} ${otherOutputInformation[i][6]}`;
                currentResponseTag.innerHTML = "P" + (otherOutputInformation[i][0]) + ":<span class=\"space\">" + calculationText + "</span>";
            }
        }
        if (proc == sorted.length - 1)
            displayResponseAverage();
    }
}

function displayResponseAverage() {

    // Response Average
    let numerator = document.getElementById('numeratorResponse');
    let denominator = document.getElementById('denominatorResponse');
    let result = document.getElementById('responseAverageResult');
    let responseText = document.getElementById('averageResponseTimeText');
    let text = '';
    let average = 0;

    for (let i = 0; i < responseTimes.length; i++) {
        if (i === 0)
            text += otherOutputInformation[i][6];
        else
            text += ' + ' + otherOutputInformation[i][6];
    }

    responseText.innerHTML = "Response Average:<span class=\"space\"> </span>";
    numerator.innerHTML = text;
    denominator.innerHTML = numberOfProcesses;
    result.innerHTML = "<span class=\"space\">=</span>" + waitingInformation[0][3];

}




// Calculate Waiting Time
// Waiting Time = Exit Time - Arrival Time - Burst Time
function generateWaitingTime(proc) {

    let currentWaitTag;
    let exitTime, arrivalTime, burstTime, currentProcessIdIndex, negativeWaitTime;
    let calculationText = "";
    let minus = "<span class=\"space\">-</span>";
    let equals = "<span class=\"space\">=</span>";
    let arrow = "<span class=\"space\">-></span>";

    if (proc === -1) {
        console.log("END BUTTON PRESSED");
        for (let i = 0; i < otherOutputInformation.length; i++) {
            currentWaitTag = document.getElementById('wait_' + (i));

            if (currentWaitTag != null) {

                exitTime = sorted[i][2];
                arrivalTime = otherOutputInformation[i][1];

                currentProcessIdIndex = otherOutputInformation[i][0] - 1;
                burstTime = procHandler[currentProcessIdIndex][2];
                if ((exitTime - arrivalTime - burstTime) < 0)
                    negativeWaitTime = true;
                else
                    negativeWaitTime = false;

                if (negativeWaitTime)
                    calculationText = `${exitTime} ${minus} ${arrivalTime} ${minus} ${burstTime} ${equals} ${(exitTime - arrivalTime - burstTime)} ${arrow} ${otherOutputInformation[i][5]}`;
                else
                    calculationText = `${exitTime} ${minus} ${arrivalTime} ${minus} ${burstTime} ${equals} ${otherOutputInformation[i][5]}`;
                currentWaitTag.innerHTML = "P" + (otherOutputInformation[i][0]) + ":<span class=\"space\">" + calculationText + "</span>";
            }
        }
        displayWaitAverage();
    } else if (proc > -1) {
        for (let i = 0; i < proc + 1; i++) {
            currentWaitTag = document.getElementById('wait_' + (i));
            if (currentWaitTag != null) {
                exitTime = sorted[i][2];
                arrivalTime = otherOutputInformation[i][1];

                currentProcessIdIndex = otherOutputInformation[i][0] - 1;
                burstTime = procHandler[currentProcessIdIndex][2];
                if ((exitTime - arrivalTime - burstTime) < 0)
                    negativeWaitTime = true;
                else
                    negativeWaitTime = false;

                if (negativeWaitTime)
                    calculationText = `${exitTime} ${minus} ${arrivalTime} ${minus} ${burstTime} ${equals} ${(exitTime - arrivalTime - burstTime)} ${arrow} ${otherOutputInformation[i][5]}`;
                else
                    calculationText = `${exitTime} ${minus} ${arrivalTime} ${minus} ${burstTime} ${equals} ${otherOutputInformation[i][5]}`;
                currentWaitTag.innerHTML = "P" + (otherOutputInformation[i][0]) + ":<span class=\"space\">" + calculationText + "</span>";
            }
        }
        if (proc == sorted.length - 1)
            displayWaitAverage();
    }
}

function displayWaitAverage() {

    // Waiting Average
    let numerator = document.getElementById('numeratorWait');
    let denominator = document.getElementById('denominatorWait');
    let result = document.getElementById('waitAverageResult');
    let waitText = document.getElementById('averageWaitTimeText');
    let text = '';

    for (let i = 0; i < otherOutputInformation.length; i++) {
        if (i === 0)
            text += otherOutputInformation[i][5];
        else
            text += ' + ' + otherOutputInformation[i][5];
    }

    waitText.innerHTML = "Wait Average:<span class=\"space\"> </span>";
    numerator.innerHTML = text;
    denominator.innerHTML = numberOfProcesses;
    result.innerHTML = "<span class=\"space\">=</span>" + waitingInformation[0][1];
}

createWaitInfo();
function createWaitInfo() {

    // WAIT TIME
    let parent = document.getElementById("waitContainer");

    for (let i = 0; i < otherOutputInformation.length; i++) {
        let child = document.createElement("p");
        let textnode = document.createTextNode("P" + (otherOutputInformation[i][0]) + ":  ");
        child.setAttribute("id", "wait_" + (i));
        child.appendChild(textnode);
        child.classList.add('top-margin');

        parent.appendChild(child);
    }

    // RESPONSE TIME
    parent = document.getElementById("responseContainer");

    for (let i = 0; i < otherOutputInformation.length; i++) {
        let child = document.createElement("p");
        let textnode = document.createTextNode("P" + (otherOutputInformation[i][0]) + ":  ");
        child.setAttribute("id", "response_" + (i));
        child.appendChild(textnode);
        child.classList.add('top-margin');

        parent.appendChild(child);
    }
}

function removeWaitInfo() {
    // WAIT
    let waitContainer = document.getElementById("waitContainer");

    while(waitContainer.firstChild){
        waitContainer.removeChild(waitContainer.firstChild);
    }

    // RESPONSE
    let responseContainer = document.getElementById("responseContainer");
    while(responseContainer.firstChild){
        responseContainer.removeChild(responseContainer.firstChild);
    }
}

// Function called when the preemptive (or non-preemptive) radio button is pressed
function changePreemptiveness() {
    toggleOverlay();
    if (preemptive) {
        if (schedulingMethod === "sjf") {
            console.log("Preemptive SJF");
            updateSchedulerType(5);
            loadingData(5);
        }
        else if (schedulingMethod === "prioHigh") {
            console.log("Preemptive Priority High -> Low");
            updateSchedulerType(6);
            loadingData(6);
        }
        else if (schedulingMethod === "prioLow") {
            console.log("Preemptive Priority Low -> High");
            updateSchedulerType(7);
            loadingData(7);
        }
        else if (schedulingMethod === "rr") {
            console.log("Preemptive Round Robin");
            updateSchedulerType(4);
            loadingData(4);
        }
        else
            console.log("Error in changePreemptiveness");

    } else {
        if (schedulingMethod === "fcfs") {
            console.log("Non-preemptive FCFS");
            updateSchedulerType(0);
            loadingData(0);
        }
        else if (schedulingMethod === "sjf") {
            console.log("Non-preemptive SJF");
            updateSchedulerType(1);
            loadingData(1);
        }
        else if (schedulingMethod === "prioHigh") {
            console.log("Non-preemptive Priority High -> Low");
            updateSchedulerType(2);
            loadingData(2);
        }
        else if (schedulingMethod === "prioLow") {
            console.log("Non-preemptive Priority Low -> High");
            updateSchedulerType(3);
            loadingData(3);
        }
        else if (schedulingMethod === "rr") {
            console.log("Round Robin is not Non-Preemptive");
        }
        else
            console.log("Error in changePreemptiveness");
    }
}

// Calls PHP file updateInput.php to execute it's code
// updateInput.php executes a Java class file that updates the first character of the in.dat file
function updateSchedulerType(schedulerType) {
    fetch('updateInput.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: "text=" + schedulerType
      })
      .then(response => response.text())
      .then(data => console.log("Successful... " + data + " was added as first character"))
}

// These exported functions are used in the loading_data_backend.js to guarantee the read 
// in.dat and out.dat files are read from after the backend code completed
export { readInputDataFile, readOutputDataFile, refreshAnim };