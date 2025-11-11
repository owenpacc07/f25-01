// This file controls the visualization
// CPU Scheduli

import { loadData } from './load_data.js';

let input = [];
let output = [];
let extraOutput = [];
let overlay = document.getElementById('overlay');
overlay.style.display = 'block';
const urlParams = new URLSearchParams(window.location.search);
const user_id = urlParams.get("user_id");
const experiment_id = urlParams.get("experiment_id");
const family_id = urlParams.get("family_id");
const mechanism_id = urlParams.get("mechanism_id");

// Check for missing parameters
if (!user_id || !experiment_id || !family_id || !mechanism_id) {
    document.getElementById("text").innerText = "Missing URL parameters. Please provide user_id, experiment_id, family_id, and mechanism_id.";
    overlay.style.display = 'block';
} else {
    await loadData(user_id, experiment_id, family_id, mechanism_id).then((data) => {
        if (!data) {
            document.getElementById("text").innerText = "Could not load experiment data. Check the server or URL parameters.";
            throw new Error("Could not load experiment data");
        } else {
            overlay.style.display = 'none';
            input = data.input;
            output = data.output;
            extraOutput = data.extraOutput;
            console.log("input:", input);
            console.log("output:", output);
        }
    }).catch((error) => {
        console.error("Error loading data:", error);
        document.getElementById("text").innerText = "An error occurred while loading data.";
    });
}

// loads table with input data
function populateTable() {
    // grab table element
    let table = document.getElementById('procArea');
    input.forEach((process, i) => {
        // create row
        let newRow = document.createElement('tr');
        newRow.setAttribute('id', 'row' + i);
        newRow.style.backgroundColor = 'rgb(255, 255, 240)';

        // create cells [pid, arrival, burst, priority]
        Object.values(process).forEach((value, i) => {
            let newCell = document.createElement('td');
            newCell.innerText = value;
            newRow.appendChild(newCell);
        });

        // append row to table
        table.appendChild(newRow);
    });
}

function populateDetailedTable() {
    let detailedTable = document.getElementById('detailedOutput');
    extraOutput.forEach((process, i) => {
        let newRow = document.createElement('tr');
        newRow.setAttribute('id', 'row2' + i);

        Object.values(process).forEach((value, i) => {
            let newCell = document.createElement('td');
            newCell.innerText = value;
            newRow.appendChild(newCell);
        });
        detailedTable.appendChild(newRow);
    });
}

// initalizes space for wait time calculations
function initializeWaitTimes() {
    let waitContainer = document.getElementById('waitContainer');
    for (let i = 0; i < input.length; i++) {
        let newP = document.createElement('p');
        newP.setAttribute('id', 'wait' + i);
        newP.innerText = `P${i + 1}: `;
        waitContainer.appendChild(newP);
    }
}

// initalizes space for response time calculations
function initializeResponseTimes() {
    let responseContainer = document.getElementById('responseContainer');
    for (let i = 0; i < input.length; i++) {
        let newP = document.createElement('p');
        newP.setAttribute('id', 'response' + i);
        newP.innerText = `P${i + 1}: `;
        responseContainer.appendChild(newP);
    }
}

populateTable();
populateDetailedTable();
initializeWaitTimes();
initializeResponseTimes();



// *** ANIMIATION FUNCTIONS ***

// variables to control animation
let head = document.getElementById('head');
let body = document.getElementById('body');
let nextBurst = 0; // index of next process burst to be added to gantt chart
let timeBurst = 0;
let timeInterval = false; // used to play animation automatically
let timeInterval2 = false;
let waitTimes = [];
let responseTimes = [];


function refreshAnim() {
    // pause animation if playing
    if (timeInterval) {
        clearInterval(timeInterval);
        timeInterval = false;
    }

    // remove all burst time cells from gantt chart
    let count = document.getElementById('animationResult').rows[1].cells.length;
    for (let i = 0; i < count; i++) {
        $("#animationResult").find("td:last-child").remove();
        $("#animationResult").find("th:last-child").remove();
    }

    // reset background color of all cells in table to white
    for (let i = 0; i < input.length; i++) {
        document.getElementById("row" + i).style.backgroundColor = "rgb(255, 255, 240)";
    }

    // reset wait and response times
    waitTimes = [];
    responseTimes = [];
    for (let i = 0; i < input.length; i++) {
        // removes <span> from wait and response time
        document.getElementById("wait" + i).innerHTML = `P${i + 1}: `;
        document.getElementById("response" + i).innerHTML = `P${i + 1}: `;
    }

    refreshAverages(); 
    
    nextBurst = 0;

    /******* MAKE BUTTONS GO TO BEGINNING STATE ********/
}
function nextAnim() {
    if (nextBurst >= output.length) {
        console.log("end of animation!");
        return;
    }

    // update gantt
    let newHead = document.createElement('th');
    let newStart = document.createElement('td');
    let newFinish = document.createElement('td');
    let newBurst = output[nextBurst].burst;
    newHead.style.height = '60px';
    newHead.style.width = newBurst * 20 + 'px'; // change width based on burst time
    newHead.innerText = 'P' + output[nextBurst].pid;
    head.appendChild(newHead);
    newFinish.innerText = output[nextBurst].end;
    newStart.innerText = '0';
    head.appendChild(newHead)
    if (nextBurst == 0) {
        body.appendChild(newStart)
    }
    body.appendChild(newFinish);

    // highlight current process in table
    let pid = output[nextBurst].pid;
    for (let i = 0; i < input.length; i++) {
        let curRow = document.getElementById('row' + i);
        let curId = curRow.cells[0].innerText; // grab pid from first column of row
        if (curId == pid) {
            curRow.style.backgroundColor = 'lightgreen';
        } else {
            curRow.style.backgroundColor = 'yellow';
        }

    }

    // wait values
    let exitTime = output.findLast((process) => process.pid == pid).end; // note that this is *last* time process was in cpu
    let arrivalTime = input.find((process) => process.pid == pid).arrival;
    let burstTime = input.find((process) => process.pid == pid).burst;
    let waitTime = exitTime - arrivalTime - burstTime;
    // add wait time calculations to wait time container
    let waitCalculation = document.createElement('span');
    waitCalculation.innerText = `${exitTime} - ${arrivalTime} - ${burstTime} = ${waitTime}`;
    document.getElementById('wait' + (pid - 1)).appendChild(waitCalculation);
    waitTimes.push(waitTime); // add wait time to array for average calculation

    // response values
    let initialResponseTime = output.find((process) => process.pid == pid).start;
    let responseTime = initialResponseTime - arrivalTime;
    // add response time calculations to response time container
    let responseCalculation = document.createElement('span');
    responseCalculation.innerText = `${initialResponseTime} - ${arrivalTime} = ${responseTime}`;
    document.getElementById('response' + (pid - 1)).appendChild(responseCalculation);
    responseTimes.push(responseTime); // add response time to array for average calculation

    // display response and wait time average if last burst
    if (nextBurst == output.length - 1) {
        let waitAvg = waitTimes.reduce((a, b) => a + b, 0) / waitTimes.length;
        let responseAvg = responseTimes.reduce((a, b) => a + b, 0) / responseTimes.length;

        // display wait and response time averages
        let numeratorWait = '';
        waitTimes.forEach((time) => {
            numeratorWait += `${time} + `;
        });
        numeratorWait = numeratorWait.slice(0, -2); // remove trailing plus sign
        document.getElementById('numeratorWait').innerText = numeratorWait;
        document.getElementById('denominatorWait').innerText = waitTimes.length;
        document.getElementById('waitAverageResult').innerText = ` = ${waitAvg.toFixed(2)}`;

        let numeratorResponse = '';
        responseTimes.forEach((time) => {
            numeratorResponse += `${time} + `;
        });
        numeratorResponse = numeratorResponse.slice(0, -2); // remove trailing plus sign
        document.getElementById('numeratorResponse').innerHTML = numeratorResponse;
        document.getElementById('denominatorResponse').innerText = responseTimes.length;
        document.getElementById('responseAverageResult').innerText = ` = ${responseAvg.toFixed(2)}`;
    }

    // update buttons, counters, etc.
    nextBurst = Math.min(nextBurst + 1, output.length);
    // if (nextBurst == output.length) {
    //     changeAnimState('end');
    //     clearInterval(timeInterval); // end automatic animation if playing
    //     timeInterval = false;
    // } 
}

function backAnim() {
    // decrement nextBurst. return if 0
    nextBurst = Math.max(nextBurst - 1, 0);
    if (nextBurst < output.length) {
        // clear response and wait time averages if not at end of animation
        refreshAverages();
    }
    if (nextBurst == 0) {
        refreshAnim();
        return;
    }
    // remove last burst from gantt chart
    $("#head").find("th:last-child").remove();
    $("#body").find("td:last-child").remove();
    // update table
    let pid = output[nextBurst].pid - 1;
    for (let i = 0; i < input.length; i++) {
        let curRow = document.getElementById('row' + i);
        let curId = curRow.cells[0].innerText; // grab pid from first column of row
        if (curId == pid) {
            curRow.style.backgroundColor = 'lightgreen';
        } else {
            curRow.style.backgroundColor = 'yellow';
        }
    }

    // update wait and response times
    document.getElementById('wait' + pid).innerHTML = `P${pid + 1}: `;
    document.getElementById('response' + pid).innerHTML = `P${pid + 1}: `;
    waitTimes.pop();
    responseTimes.pop();
}

function refreshAverages() {
    document.getElementById('numeratorWait').innerText = '';
    document.getElementById('denominatorWait').innerText = '';
    document.getElementById('waitAverageResult').innerText = '';
    document.getElementById('numeratorResponse').innerText = '';
    document.getElementById('denominatorResponse').innerText = '';
    document.getElementById('responseAverageResult').innerText = '';
}

// Animation control functions for bottom data table //////////////////////
function refreshAnim2() {
    //Pause anim if playing
    if (timeInterval2) {
        clearInterval(timeInterval2);
        timeInterval2 = false;
    }

    // reset background color of all cells in table to white
    for (let i = 0; i < extraOutput.length; i++) {
        document.getElementById("row2" + i).style.backgroundColor = "rgb(255, 255, 240)";
    }

    timeBurst = 0;
}


function nextAnim2() { 
    //Highlight the current time in the extra data table 
    let timeId = extraOutput[timeBurst].time;
    for (let i = 0; i < extraOutput.length; i++){
        let currRow = document.getElementById('row2' + i);
        let currId = currRow.cells[0].innerText; // grab timeid from first column of row
        if (currId == timeId) {
            currRow.style.backgroundColor = 'lightgreen';
        } else {
            currRow.style.backgroundColor = 'yellow';
        }
    }
    timeBurst = Math.min(timeBurst + 1, extraOutput.length);
}

function backAnim2() {
    timeBurst = Math.max(timeBurst - 1, 0);
    let timeId = extraOutput[timeBurst].time - 1;
    for (let i = 0; i < extraOutput.length; i++) {
        let curRow = document.getElementById('row2' + i);
        let curId = curRow.cells[0].innerText; 
        if (curId == timeId) {
            curRow.style.backgroundColor = 'lightgreen';
        } else {
            curRow.style.backgroundColor = 'yellow';
        }
    }
}
/*****************************************************
 * 
 * Animation button controls
 * 
 *****************************************************/

let playButton = document.getElementById('play');
playButton.onclick = () => {
    if (timeInterval) {
        playButton.innerText = 'Play';
        clearInterval(timeInterval);
        timeInterval = false;
    } else {
        playButton.innerText = 'Pause';
        timeInterval = setInterval(nextAnim, 1000);
    }
}
let nextButton = document.getElementById('stepForward');
nextButton.onclick = nextAnim;
let backButton = document.getElementById('stepBack');
backButton.onclick = backAnim;
let resetButton = document.getElementById('skipBack');
resetButton.onclick = refreshAnim;
let endButton = document.getElementById('skipForward');
endButton.onclick = () => {
    while (nextBurst < output.length) {
        nextAnim();
    }
}

// Bottom table button controls
let playButton2 = document.getElementById('play2');
playButton2.onclick = () => {
    if (timeInterval2) {
        playButton2.innerText = 'Play';
        clearInterval(timeInterval2);
        timeInterval2 = false;
    } else {
        playButton2.innerText = 'Pause';
        timeInterval2 = setInterval(nextAnim2, 1000);
    }
} 
let nextButton2 = document.getElementById('stepForward2');
nextButton2.onclick = nextAnim2;
let backButton2 = document.getElementById('stepBack2');
backButton2.onclick = backAnim2;
let resetButton2 = document.getElementById('skipBack2');
resetButton2.onclick = refreshAnim2;
let endButton2 = document.getElementById('skipForward2');
endButton2.onclick = () => {
    while (timeBurst < extraOutput.length) {
        nextAnim2();
    }
}