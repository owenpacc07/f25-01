import { readInputDataFile, readOutputDataFile, refreshAnim } from "./main.js";

// JavaScript for how the front-end (webpage) responds when input/output files are being updated on the backend

var flagFileUpdated = false;
var interval;


function toggleOverlay() {
    document.getElementById("overlay").style.display = "block";
}

function loadingData(schedulerType) {
    interval = setInterval(checkFlagFile, 2000, schedulerType);
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
        readInputDataFile("../../files/p3/in.dat");
        readOutputDataFile("../../files/p3/out.dat");

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

export { toggleOverlay, loadingData };