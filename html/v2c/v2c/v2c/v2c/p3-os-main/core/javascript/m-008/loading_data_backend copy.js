
import { readInputDataFile, readOutputDataFile, refreshAnim } from "./main.js";

// JavaScript for how the front-end (webpage) responds when input/output files are being updated on the backend
//m-001
var flagFileUpdated = false;
var interval;

// address to the other CPU Scheduling pages
let first_come_first_serve = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-001.php';
let shortest_job_first = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-002.php';
let priority_high_to_low = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-003.php';
let priority_low_to_high = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-004.php';
let round_robin = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-005.php';

// Input and output file paths
let input_file_path = 'https://cs.newpaltz.edu/p/f22-02/files/p3/in.dat';
let output_file_path = 'https://cs.newpaltz.edu/p/f22-02/files/p3/out.dat';


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
        readInputDataFile(input_file_path);
        readOutputDataFile(output_file_path);

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

    if (stepByStep) {
        document.getElementById('start').disabled = false;
        document.getElementById('play').disabled = true;
    }
    else {
        document.getElementById('play').disabled = false;
        document.getElementById('start').disabled = true;
    }
}

export { toggleOverlay, loadingData };