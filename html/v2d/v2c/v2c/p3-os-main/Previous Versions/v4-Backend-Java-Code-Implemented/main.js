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
var sortedLine = [];
var sorted = [];
var otherOutputInformation = [];
var outputLine = [];
let waitingInformation = [];
let responseTimes = [];
let schedulingMethod = "fcfs";

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

function changeSchedulerType(schedulerType) {

}

// New function to read in.dat file as input
function readInputDataFile(file) {
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

                                        if (outputLine.length == 6) {
                                            otherOutputInformation.push(outputLine);
                                            outputLine = [];
                                        }
                                    });
                                } else {
                                    nextNum = Number(outputString);
                                    outputLine.push(nextNum);

                                    if (outputLine.length == 2) {
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
}

readInputDataFile("../../files/p3/in.dat");

var numberOfProcesses = procHandler.length;
const processInfo = 4;

function loadInputDataTable() {
    for (i = 0; i < numberOfProcesses; i++) {
        var newRow = document.createElement('tr');
        newRow.setAttribute('id', 'row' + i);
        procBody.appendChild(newRow);
        newRow.style.cssText = 'background-color: rgb(255, 255, 240);';
        for (j = 0; j < processInfo; j++) {
            var cell = newRow.insertCell();
            cell.innerHTML = procHandler[i][j];
        }
    }
}

loadInputDataTable();

var preemptive = false;
$('input[type=radio][name=preType]').change(function () {
    if (this.value == 'nonpre') {
        preemptive = false;
    }
    else if (this.value == 'pre') {
        preemptive = true;
    }
    changePreemptiveness();
});

//animation type 
$('input[type=radio][name=animationType]').change(function () {
    if (this.value == 'StepByStep') {
        //disable play/pause
        document.getElementById('play').setAttribute('disabled', 'disabled')
        document.getElementById('pause').setAttribute('disabled', 'disabled')
        document.getElementById('next').removeAttribute("disabled");
        document.getElementById('back').removeAttribute("disabled");
        //document.getElementById('play').style.background ="grey"
        //document.getElementById('pause').style.background = "grey"
        //document.getElementById('next').classList.add("btn-primary");
        // document.getElementById('back').classList.add("btn-primary");
        // document.getElementById('next').removeProperty('background')
        // document.getElementById('back').removeProperty('background')



    }
    else if (this.value == 'Automatic') {
        //disable next/back
        document.getElementById('next').setAttribute('disabled', 'disabled')
        document.getElementById('back').setAttribute('disabled', 'disabled')
        document.getElementById('play').removeAttribute("disabled");
        document.getElementById('pause').removeAttribute("disabled");
        // document.getElementById('next').style.background ="grey"
        //document.getElementById('back').style.background = "grey"
        //document.getElementById('play').removeProperty('background')
        // document.getElementById('pause').removeProperty('background')
    }
});





// First Come First Serve
var fcfs = document.getElementById('fcfs');
fcfs.addEventListener("click", fcfsClick);
function fcfsClick(e) {

    schedulingMethod = "fcfs";

    if (preemptive) {
        console.log("Preemptive FCFS -- FCFS not implemented in Scheduler.java so there 1 is input into in.dat");
        updateSchedulerType(1);
    } else {
        console.log("Non-preemptive FCFS -- FCFS not implemented in Scheduler.java so there 1 is input into in.dat");
        updateSchedulerType(1);
    }

    refreshAnim();
    sorted = [];
    sortedLine = [];

    readOutputDataFile("../../files/p3/out.dat");

    var slice = document.getElementById("slice");
    if (slice != null)
        slice.remove();

    document.getElementById('fcfs').style.border = "2px solid dimgray";
    document.getElementById('sjf').style.border = "none";
    document.getElementById('prio').style.border = "none";
    document.getElementById('rr').style.border = "none";

    document.getElementById('start').disabled = false;

    //document.getElementById('fcfs').toggle();


    // document.getElementById('fcfs').style.backgroundColor = "rgb(10, 0, 77)";
    // document.getElementById('sjf').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('prio').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('rr').style.backgroundColor = "rgb(86, 60, 255)";

}

// Shortest Job First
var sjf = document.getElementById('sjf');
sjf.addEventListener("click", sjfClick);
function sjfClick(e) {

    schedulingMethod = "sjf";

    if (preemptive) {
        console.log("Preemptive SJF");
        updateSchedulerType(5);
    } else {
        console.log("Non-preemptive SJF");
        updateSchedulerType(1);
    }

    refreshAnim();
    sorted = [];
    sortedLine = [];

    readOutputDataFile("../../files/p3/out.dat");

    var slice = document.getElementById("slice");
    if (slice != null)
        slice.remove();
    document.getElementById('fcfs').style.border = "none";
    document.getElementById('sjf').style.border = "2px solid dimgray";
    document.getElementById('prio').style.border = "none";
    document.getElementById('rr').style.border = "none";

    document.getElementById('start').disabled = false;

    //document.getElementById('sjf').toggle();

    // document.getElementById('fcfs').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('sjf').style.backgroundColor = "rgb(10, 0, 77)";
    // document.getElementById('prio').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('rr').style.backgroundColor = "rgb(86, 60, 255)";

}


// Priority
var prio = document.getElementById('prio');
prio.addEventListener("click", prioClick);
function prioClick(e) {

    schedulingMethod = "prio"

    if (preemptive) {
        console.log("Preemptive Priority High -> Low");
        updateSchedulerType(6);
    } else {
        console.log("Non-preemptive Priority High -> Low");
        updateSchedulerType(2);
    }

    refreshAnim();

    sorted = [];
    sortedLine = [];
    if (preemptive == false)
        readOutputDataFile("../../files/p3/out.dat");
    else if (preemptive == true)
        readOutputDataFile("../../files/p3/out.dat");
    var slice = document.getElementById("slice");
    if (slice != null)
        slice.remove();
    document.getElementById('fcfs').style.border = "none";
    document.getElementById('sjf').style.border = "none";
    document.getElementById('prio').style.border = "2px solid dimgray";
    document.getElementById('rr').style.border = "none";

    document.getElementById('start').disabled = false;

    // document.getElementById('fcfs').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('sjf').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('prio').style.backgroundColor = "rgb(10, 0, 77)";
    // document.getElementById('rr').style.backgroundColor = "rgb(86, 60, 255)";

}


// Round Robin
var rr = document.getElementById('rr');
rr.addEventListener("click", rrClick);
function rrClick(e) {

    if (preemptive) {
        console.log("Preemptive Round Robin");
        updateSchedulerType(4);
    }

    schedulingMethod = "rr";

    refreshAnim();

    sorted = [];
    sortedLine = [];
    var slice = document.createElement("span");
    slice.setAttribute('id', 'slice');
    var spot = document.getElementById("timeSlice");
    spot.appendChild(slice);
    slice.innerText = "Time Slice: 4";
    readOutputDataFile("../../files/p3/out.dat");

    document.getElementById('fcfs').style.border = "none";
    document.getElementById('sjf').style.border = "none";
    document.getElementById('prio').style.border = "none";
    document.getElementById('rr').style.border = "2px solid dimgray";

    document.getElementById('start').disabled = false;

    // document.getElementById('fcfs').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('sjf').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('prio').style.backgroundColor = "rgb(86, 60, 255)";
    // document.getElementById('rr').style.backgroundColor = "rgb(10, 0, 77)";

}




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


        // resets average result
        if (i == otherOutputInformation.length - 1) {

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
    }
}

function refreshAnim() {
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
    document.getElementById('start').disabled = true;
    document.getElementById('next').disabled = false;
    document.getElementById('back').disabled = true;
    document.getElementById('end').disabled = false;
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

    var currentProc = sorted[procIndex][0];
    currentProc -= 2;
    var currentRow = document.getElementById("row" + currentProc);
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

    if (procIndex == 0) {
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
    timeInterval = setInterval(function () {
        procNum += 1;
        var table = document.getElementById("animationResult");
        var head = document.getElementById("head");
        var body = document.getElementById("body");
        var newHead = document.createElement('th');
        var newStart = document.createElement('td');
        var newFinish = document.createElement('td');

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
        //procIndex += 1;
    }, 1000)
});

var pause = document.getElementById("pause");
pause.addEventListener("click", function () {
    clearInterval(timeInterval);
})


//======================================================================================
console.log("Process Count: " + procHandler.length);


function removeAllChildNodes(parent) {
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }
}


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
                    calculationText = `${processInitialCPUTime} ${minus} ${arrivalTime} ${equals} ${(processInitialCPUTime - arrivalTime)} ${arrow} 0`;
                else
                    calculationText = `${processInitialCPUTime} ${minus} ${arrivalTime} ${equals} ${(processInitialCPUTime - arrivalTime)}`;
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
            text += responseTimes[i];
        else
            text += ' + ' + responseTimes[i];

        average += responseTimes[i];
    }

    average /= numberOfProcesses;

    responseText.innerHTML = "Response Average:<span class=\"space\"> </span>";
    numerator.innerHTML = text;
    denominator.innerHTML = numberOfProcesses;
    result.innerHTML = "<span class=\"space\">=</span>" + average;

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
                //currentWaitTag.innerHTML = "P" + (otherOutputInformation[i][0]) + ":<span class=\"space\">" + (otherOutputInformation[i][5] + "</span>");
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
                //currentWaitTag.innerHTML = "P" + (otherOutputInformation[i][0]) + ":<span class=\"space\">" + (otherOutputInformation[i][5] + "</span>");
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

    readOutputDataFile("../../files/p3/out.dat");

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

function changePreemptiveness() {
    if (preemptive) {
        if (schedulingMethod === "fcfs") {
            console.log("Preemptive FCFS -- FCFS not implemented in Scheduler.java so there 1 is input into in.dat");
            updateSchedulerType(1);
        } 
        else if (schedulingMethod === "sjf") {
            console.log("Preemptive SJF");
            updateSchedulerType(5);
        }
        else if (schedulingMethod === "prio") {
            console.log("Preemptive Priority High -> Low");
            updateSchedulerType(6);
        }
        else if (schedulingMethod === "rr") {
            console.log("Preemptive Round Robin");
            updateSchedulerType(4);
        }
        else
            console.log("Error in changePreemptiveness");

    } else {
        if (schedulingMethod === "fcfs") {
            console.log("Non-preemptive FCFS -- FCFS not implemented in Scheduler.java so there 1 is input into in.dat");
            updateSchedulerType(1);
        }
        else if (schedulingMethod === "sjf") {
            console.log("Non-preemptive SJF");
            updateSchedulerType(1);
        }
        else if (schedulingMethod === "prio") {
            console.log("Non-preemptive Priority High -> Low");
            updateSchedulerType(2);
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
      .then(data => console.log("Successful... " + data + " was added as first character"));

    // Prepares variables to display new output information
    removeWaitInfo();
    refreshAnim();
    
    readInputDataFile("../../files/p3/in.dat");
    readOutputDataFile("../../files/p3/out.dat");
    
    createWaitInfo();
}