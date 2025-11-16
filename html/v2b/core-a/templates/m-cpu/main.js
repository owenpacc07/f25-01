// This file controls the visualization
// CPU Scheduling

import { loadData } from './load_data.js';

let input = [];
let output = [];
let overlay = document.getElementById('overlay');
overlay.style.display = 'block';

await loadData()
    .then((data) => {
        if (!data) {
            document.getElementById('text').innerText = 'Could not load output data';
            throw new Error('Could not load output data');
        } else {
            overlay.style.display = 'none';
            input = data.input;
            output = data.output;
        }
    });

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
populateTable();



// *** ANIMIATION FUNCTIONS ***

// variables to control animation
let result = document.getElementById('animationResult');
let head = document.getElementById('head');
let body = document.getElementById('body');
let nextBurst = 0; // index of next process burst to be added to gantt chart
let timeInterval = false; // used to play animation automatically


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

    // Refreshes the waiting and response calculations
    // refreshWaitAndResponse();
    // removeWaitInfo();
    // createWaitInfo();
    // refreshAverages();


    nextBurst = 0;
    // procNum = -1;

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
    // update buttons, counters, etc.
    nextBurst = Math.min(nextBurst + 1, output.length);
    // if (nextBurst == output.length) {
    //     changeAnimState('end');
    //     clearInterval(timeInterval); // end automatic animation if playing
    //     timeInterval = false;
    // }
}

function backAnim() {
    // remove last burst from gantt chart
    nextBurst = Math.max(nextBurst - 1, 0);
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
}




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
