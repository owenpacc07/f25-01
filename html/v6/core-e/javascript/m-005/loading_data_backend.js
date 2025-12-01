
import { readInputDataFile, readOutputDataFile, refreshAnim } from "./main.js";

// JavaScript for how the front-end (webpage) responds when input/output files are being updated on the backend

var flagFileUpdated = false;
var interval;

// Input and output file paths
let input_file_path = 'https://cs.newpaltz.edu/p/f22-02/files/core/m-005/in-005.dat';
let output_file_path = 'https://cs.newpaltz.edu/p/f22-02/files/core/m-005/out-005.dat';


function toggleOverlay() {
    document.getElementById("overlay").style.display = "block";
}

function loadingData(schedulerType) {
    interval = setInterval(checkFlagFile, 500, schedulerType);
}

var attempts = 5;
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

        mechanismRefresh();

        console.log("FILES UPDATED");
        // disables overlay
        document.getElementById("overlay").style.display = "none";
    }
    // if keeps failing, stop and let user know
    if (attempts <= 0) {
        clearInterval(interval)
        document.getElementById("text").innerText = "Could not load output data";
        return;
    } 
    // otherwise decrement attempts
    attempts -= 1;
    console.log("trying again...")
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
    fetch('https://cs.newpaltz.edu/p/f22-02/v2/core/php/m-005/manage-flag-file-005.php', {
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


function mechanismRefresh() {
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


// function rrRefresh() {
//     refreshAnim();

//     let stepByStep;
//     if (document.getElementById('step-by-step').checked == true)
//         stepByStep = true;
//     else
//         stepByStep = false;

//     var slice = document.createElement("span");
//     slice.setAttribute('id', 'slice');
//     // var spot = document.getElementById("timeSlice");
//     // spot.appendChild(slice);
//     slice.innerText = "Time Slice: 4";

//     document.getElementById('fcfs').style.border = "none";
//     document.getElementById('sjf').style.border = "none";
//     document.getElementById('prioHigh').style.border = "none";
//     document.getElementById('prioLow').style.border = "none";
//     document.getElementById('rr').style.border = "2px solid dimgray";

//     if (stepByStep) {
//         document.getElementById('start').disabled = false;
//         document.getElementById('play').disabled = true;
//     }
//     else {
//         document.getElementById('play').disabled = false;
//         document.getElementById('start').disabled = true;
//     }
// }

export { toggleOverlay, loadingData };